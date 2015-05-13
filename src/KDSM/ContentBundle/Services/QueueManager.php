<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/28/2015
 * Time: 9:11 AM
 */

namespace KDSM\ContentBundle\Services;

use Doctrine\ORM\EntityManager;
use KDSM\ContentBundle\Entity\Queue;
use KDSM\ContentBundle\Entity\UsersQueues;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\EventDispatcher\GenericEvent;

class QueueManager
{
    private $entityManager;
    private $eventDispatcher;

    private $queueRepository;
    private $userRepository;
    private $usersQueuesRepository;
    private $notificationRepository;

    /**
     * @param EntityManager $entityManager
     * @param $eventDispatcher
     */
    public function __construct(EntityManager $entityManager, $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->queueRepository = $this->entityManager->getRepository('KDSMContentBundle:Queue');
        $this->userRepository = $this->entityManager->getRepository('KDSMContentBundle:User');
        $this->usersQueuesRepository = $this->entityManager->getRepository('KDSMContentBundle:UsersQueues');
        $this->notificationRepository = $this->entityManager->getRepository('KDSMContentBundle:Notification');
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getCurrentQueueList($userId)
    {
        return $this->queueRepository->getCurrentQueue($userId);
    }

    public function getSingleQueue($queueId, $userId)
    {
        return $this->queueRepository->getSingleQueue($queueId, $userId);
    }

    /**
     * @param $users
     * @param $ownerId
     * @return null
     */
    public function queueCreateRequest($users, $ownerId)
    {
        $queueObject = new Queue();
        $queueObject->setStatus('creatingGame');
        $queueObject->setReservationDateTime(new \DateTime());
        $this->queueRepository->persistObject($queueObject);

        $this->sendInvites($users, $ownerId, $queueObject->getId());

        foreach ($users as $user) {
            $this->createUserQueue($user, $ownerId, $queueObject);
        }
        $this->entityManager->detach($queueObject);

        return $this->parseQueue($queueObject);
    }

    /**
     * @param $user
     * @param $ownerId
     * @param Queue $queueObject
     */
    private function createUserQueue($user, $ownerId, $queueObject)
    {
        $userQueues = new UsersQueues();

        $userObject = $this->userRepository->findOneBy(array('id' => $user));
        if ($userObject != null) {
            $userQueues->setUser($userObject);
            $userObject->addUsersQueue($userQueues);

            $userObject->getId() == $ownerId ? $userQueues->setUserStatusInQueue('queueOwner') :
                $userQueues->setUserStatusInQueue('invitePending');
        }

        $queueObject = $this->queueRepository->findOneBy(array('id' => $queueObject->getId()));
        $userQueues->setQueue($queueObject);
        $queueObject->addUsersQueue($userQueues);

        $this->usersQueuesRepository->persistObject($userQueues);
        $this->entityManager->detach($userQueues);
    }

    /**
     * @param $usersIds
     * @param $ownerId
     * @param $gameId
     */
    public function sendInvites($usersIds, $ownerId, $gameId)
    {
        foreach ($usersIds as $userId) {
            if ($userId != $ownerId) {
                $event = new GenericEvent();
                $event->setArgument('gameid', $gameId);
                $event->setArgument('userid', $userId);
                $this->eventDispatcher->dispatch('kdsm_content.notification_create', $event);
            }
        }
    }

    /**
     * @param Queue $queue
     * @return null
     */
    private function parseQueue($queue)
    {
        $response = null;
        if ($queue != null) {
            $response['queueId'] = $queue->getId();
            $response['queueStatus'] = $queue->getStatus();
            $response['queueCreateTime'] = $queue->getReservationDateTime();
            $usersQueues = $queue->getUsersQueues();
            foreach ($usersQueues as $key => $uq) {
                $response['players'][$key]['userId'] = $uq->getUser()->getId();
                $response['players'][$key]['userName'] = $uq->getUser()->getUsername();
                $response['players'][$key]['userPicturePath'] = $uq->getUser()->getProfilePicturePath();
                $response['players'][$key]['userStatus'] = $uq->getUserStatusInQueue();
            }
        }

        return $response;
    }

    /**
     * @param $users
     * @param $queueId
     * @return null
     */
    public function queueAddUsersRequest($usersList, $queueId)
    {
        $queueObject = $this->queueRepository->findOneBy(array('id' => $queueId));
        $usersQueues = $queueObject->getUsersQueues();
        $existingUsers = null;
        //todo i metodus
//        $usersList = $this->filterAlreadyInvitedUsers($usersList, $usersQueues);

        foreach ($usersQueues as $userQueue) {
            $userId = $userQueue->getUser()->getId();
            if (in_array($userId, $usersList)) {
                if (($key = array_search($userId, $usersList)) !== false) {
                    $existingUsers[] = $usersList[$key];
                    unset($usersList[$key]);
                }
            }
        }

        $this->removeKickedUsers($usersQueues, $existingUsers, $queueObject);

        if (!empty($usersList)) {
            $response = null;
            foreach ($usersList as $user) {
                $this->sendInvites($usersList, null, $queueObject->getId());
                $this->createUserQueue($user, null, $queueObject);
            }
            $response['inviteStatus'] = 'SUCCESS';
        } else {
            $response['inviteStatus'] = 'NO NEW USERS';
        }

        return $response;
    }

    private function filterAlreadyInvitedUsers($users, $usersQueues)
    {
        foreach ($usersQueues as $userQueue) {
            $userId = $userQueue->getUser()->getId();
            if (in_array($userId, $users)) {
                if (($key = array_search($userId, $users)) !== false) {
                    $existingUsers[] = $users[$key];
                    unset($users[$key]);
                }
            }
        }
        return $users;
    }

    private function removeKickedUsers($usersQueues, $existingUsers, $queueObject)
    {
        foreach ($usersQueues as $userQueue) {
            $userId = $userQueue->getUser()->getId();
            if ($this->isKicked($existingUsers, $userId, $userQueue)) {
                $queueObject->removeUsersQueue($userQueue);
                $this->usersQueuesRepository->deleteObject($userQueue);
            }
        }
    }

    private function isKicked($existingUsers, $userId, $userQueue)
    {
        return (($existingUsers == null || !in_array($userId, $existingUsers))
            && $userQueue->getUserStatusInQueue() != 'queueOwner') ? true : false;
    }

    /**
     * @param $queueId
     * @param $userId
     * @return null
     */
    public function removeQueue($queueId, $userId)
    {
        $response['deleteStatus'] = 'ERROR: something went very wrong here';

        $queueObject = $this->queueRepository->findOneBy((array('id' => $queueId)));
        if ($queueObject != null) {
            if ($queueObject->getStatus() == 'deleted') {
                $response['deleteStatus'] = 'ERROR: the queue is already deleted.';
            } elseif ($this->getQueueOwner($queueObject) == $userId) {
                $usersQueues = $queueObject->getUsersQueues();
                foreach ($usersQueues as $usersQueue) {
                    $usersQueue->setUserStatusInQueue('canceled');
                }
                $notifications = $this->notificationRepository->findBy((array('gameId' => $queueId)));
                if ($notifications != null) {
                    foreach ($notifications as $notification) {
                        $this->notificationRepository->setViewed($notification);
                    }
                }
                $queueObject->setStatus('deleted');
                $this->queueRepository->persistObject($queueObject);
                $response['deleteStatus'] = 'SUCCESS';
            } else {
                $response['deleteStatus'] = 'ERROR: the user does not have the permissions to delete this queue.';
            }
        } else {
            $response['deleteStatus'] = 'ERROR: the queue does not exist.';
        }

        return $response;
    }

    /**
     * @param Queue $queueObject
     * @return null
     */
    private function getQueueOwner($queueObject)
    {
        $queueOwner = null;
        $usersQueues = $queueObject->getUsersQueues();
        foreach ($usersQueues as $usersQueue) {
            if ($usersQueue->getUserStatusInQueue() == 'queueOwner') {
                $queueOwner = $usersQueue->getUser()->getId();
                break;
            }
        }
        return $queueOwner;
    }

    /**
     * @param $queueId
     * @param $userId
     * @param $response
     */
    public function processUserInviteResponse($queueId, $userId, $response)
    {
        $queueJoinResponse['response'] = 'ERROR: I did not understand the response you sent me.';

        $queueObject = $this->queueRepository->findOneBy((array('id' => $queueId)));
        $userObject = $this->userRepository->findOneBy((array('id' => $userId)));
        $userQueueObject = $this->usersQueuesRepository->getObjectByIds($queueObject, $userObject);
        $notificationObject = $this->notificationRepository->findOneBy((array(
            'gameId' => $queueId,
            'userId' => $userId
        )));

        $this->notificationRepository->setViewed($notificationObject);

        if ($response == 'accepted') {
            $acceptedUserCountInQueue = $this->usersQueuesRepository->getAcceptedUserCount($queueObject);
            if ($acceptedUserCountInQueue < 3) {
                $userQueueObject->setUserStatusInQueue('inviteAccepted');
                if ($acceptedUserCountInQueue == 1) {
                    $queueObject->setStatus('in_queue');
                }
                $queueJoinResponse['response'] = 'Accept SUCCESS';
            } else {
                $userQueueObject->setUserStatusInQueue('inviteDeclined');
                $queueJoinResponse['response'] = 'Accept FAIL: Queue Full';
            }
        }

        if ($response == 'declined') {
            $userQueueObject->setUserStatusInQueue('inviteDeclined');
            $queueJoinResponse['response'] = 'Decline SUCCESS';
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
        return $queueJoinResponse;
    }

    /**
     * @return bool
     */
    public function setNextQueueAsActive()
    {
        $isQueue = false;
        $queueObject = $this->queueRepository->findOneBy(array('status' => 'in_queue'));
        if ($queueObject != null) {
            $queueObject->setStatus('active');
            $this->queueRepository->persistObject($queueObject);
            $isQueue = true;
        }
        return $isQueue;
    }

    public function setActiveQueueAsExpired()
    {
        $queueObjects = $this->queueRepository->findBy(array('status' => 'active'));
        if ($queueObjects != null) {
            foreach ($queueObjects as $queueObject) {
                $queueObject->setStatus('expired');
                $this->queueRepository->persistObject($queueObject);
            }
        }
    }
}

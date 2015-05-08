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
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;

class QueueManager extends ContainerAwareCommand
{
    private $entityManager;
    private $eventDispatcher;

    private $queueRepository;

    public function __construct(EntityManager $entityManager, $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->queueRepository = $this->entityManager->getRepository('KDSMContentBundle:Queue');
    }

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

        $this->sendInvites($users, $queueObject->getId());

        $userRepository = $this->entityManager->getRepository('KDSMContentBundle:User');

        $usersQueuesRepository = $this->entityManager->getRepository('KDSMContentBundle:UsersQueues');

        foreach ($users as $user) {
            $userQueues = new UsersQueues();

            $userObject = $userRepository->findOneBy(array('id' => $user));
            if ($userObject != null) {
                $userQueues->setUser($userObject);
                $userObject->addUsersQueue($userQueues);

                $userObject->getId() == $ownerId ? $userQueues->setUserStatusInQueue('queueOwner') :
                    $userQueues->setUserStatusInQueue('invitePending');
            }

            $queueObject = $this->queueRepository->findOneBy(array('id' => $queueObject->getId())); //reikia nes per daug em->clear'u
            $userQueues->setQueue($queueObject);
            $queueObject->addUsersQueue($userQueues);

            $usersQueuesRepository->persistObject($userQueues);
        }
        $this->entityManager->detach($queueObject);
        $this->entityManager->detach($userQueues);

        return $this->parseQueue($queueObject);
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
     * @param $usersIds
     * @param $gameId
     */

    public function sendInvites($usersIds, $gameId)
    {
        foreach ($usersIds as $userId) {
            $event = new GenericEvent();
            $event->setArgument('gameid', $gameId);
            $event->setArgument('userid', $userId);
            $this->eventDispatcher->dispatch('kdsm_content.notification_create', $event);
        }
    }

    public function removeQueue($queueId, $userId)
    {
        $notificationRepository = $this->entityManager->getRepository('KDSMContentBundle:Notification');

        $response = null;

        $queueObject = $this->queueRepository->findOneBy((array('id' => $queueId)));
        if ($queueObject != null) {
            if ($queueObject->getStatus() == 'deleted') {
                $response['deleteStatus'] = 'ERROR: the queue is already deleted.';
            } else if ($this->getQueueOwner($queueObject) == $userId) {
                $usersQueues = $queueObject->getUsersQueues();
                foreach ($usersQueues as $usersQueue) {
                    $usersQueue->setUserStatusInQueue('canceled');
                }

                $notifications = $notificationRepository->findBy((array('gameId' => $queueId)));
                if ($notifications != null) {
                    foreach ($notifications as $notification) {
                        $notification->setViewed(1);
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
     * @param $queueObject
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
     * @param Queue $queue
     * @return bool
     */
    private function getIsFull($queue)
    {
        $isFull = false;
//        if (count($queue->getUsers()) == 2 && !$queue->getIsFourPlayers()) {
//            $isFull = true;
//        }
//        if (count($queue->getUsers()) == 4 && !$queue->getIsFourPlayers()) {
//            $isFull = true;
//        }
        return $isFull;
    }

    /**
     * @param $queue
     * @param $user
     * @return bool
     */
    private function getIsAlreadyInQueue($queue, $user)
    {
        $isInQueue = false;
//        foreach ($queue->getUsers() as $u) {
//            if ($u->getId() == $user->getId()) {
//                $isInQueue = true;
//            }
//        }
        return $isInQueue;
    }
}
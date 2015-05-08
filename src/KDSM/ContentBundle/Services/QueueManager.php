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

    public function getCurrentQueueList()
    {
        return $this->queueRepository->getCurrentQueue();
    }

    /**
     * @param $users
     */
    public function queueCreateRequest($users)
    {
        $queueObject = new Queue();
        $queueObject->setStatus('creatingGame');
        $queueObject->setReservationDateTime(new \DateTime());
        $this->queueRepository->persistObject($queueObject);
        $userRepository = $this->entityManager->getRepository('KDSMContentBundle:User');

        $usersQueuesRepository = $this->entityManager->getRepository('KDSMContentBundle:UsersQueues');

//        if (!$queue) //neveikia
//            throw new NotFoundHttpException('Page not found');
//        if($this->getIsFull($queue))
//            return $queue;//'full';
        foreach ($users as $user) {
            $userQueues = new UsersQueues();

            $userObject = $userRepository->findOneBy(array('id' => $user));
            if ($userObject != null) {
                $userQueues->setUser($userObject);
                $userObject->addUsersQueue($userQueues);
            }

            $userQueues->setUserStatusInQueue('invitePending');

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
     * @param $users
     * @param $queue
     * @param $dispatcher
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
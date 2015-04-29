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
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class QueueManager extends ContainerAwareCommand
{
    private $entityManager;

    private $queueRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->queueRepository = $this->entityManager->getRepository('KDSMContentBundle:Queue');
    }

    public function getCurrentQueueList()
    {
        return $this->queueRepository->getCurrentQueue();
    }

    public function createNewQueueElement($user)
    {
        $queueElem = new Queue();
        $queueElem->setReservationDateTime(new \DateTime('now'));
        $queueElem->setIsFourPlayers(false);
        $queueElem->addUser($user);
        $queueElem->setStatus('pending');

        $this->queueRepository->persistObject($queueElem);
        return $queueElem;
    }

    public function joinQueueRequest($queueId, $user)
    {
        $queue = $this->queueRepository->findOneBy(array('id' => $queueId));
        if (!$queue)
            return 404;
        if($this->getIsFull($queue))
            return $queue;//'full';
        if($this->getIsAlreadyInQueue($queue, $user))
            return $queue; //'alreadyInQueue';
        $queue->addUser($user);
        $this->queueRepository->persistObject($queue);
        return $queue;
    }

    private function getIsFull($queue)
    {
        $isFull = false;
        if (count($queue->getUsers()) == 2 && !$queue->getIsFourPlayers())
            $isFull = true;
        if (count($queue->getUsers()) == 4 && !$queue->getIsFourPlayers())
            $isFull = true;
        return $isFull;
    }

    private function getIsAlreadyInQueue($queue, $user)
    {
        $isInQueue = false;
        foreach ($queue->getUsers() as $u)
            if ($u->getId() == $user->getId())
                $isInQueue = true;
        return $isInQueue;
    }

}
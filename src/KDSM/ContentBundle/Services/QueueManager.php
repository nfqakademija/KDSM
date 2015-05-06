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

        $array = array();
        $array['id'] = $queueElem->getId();
        $array['success'] = true;
        return $array;
        //return $queueElem;
    }

    public function queueCreateRequest($users)
    {
        $queueObject = new Queue();
        $this->queueRepository->persistObject($queueObject);
        $userRepository = $this->entityManager->getRepository('KDSMContentBundle:User');

        $usersQueuesRepository = $this->entityManager->getRepository('KDSMContentBundle:UsersQueues');

//        if (!$queue) //neveikia
//            throw new NotFoundHttpException('Page not found');
//        if($this->getIsFull($queue))
//            return $queue;//'full';
        foreach ($users as $user)
        {
            $userQueues = new UsersQueues();

            $userObject = $userRepository->findOneBy(array('id' => $user));
            if ($userObject != null)
            {
                $userQueues->setUser($userObject);
                $userObject->addUsersQueue($userQueues);
            }

            $userQueues->setUserStatusInQueue('invitePending');

            $queueObject = $this->queueRepository->findOneBy(array('id' => $queueObject->getId())); //reikia nes per daug em->clear'u
            $userQueues->setQueue($queueObject);
            $queueObject->addUsersQueue($userQueues);

            $usersQueuesRepository->persistObject($userQueues);
        }

        return $queueObject;
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
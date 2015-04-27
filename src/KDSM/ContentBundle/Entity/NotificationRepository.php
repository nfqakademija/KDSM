<?php

namespace KDSM\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\False;

/**
 * NotificationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificationRepository extends EntityRepository
{

    public function getAllUnviewedNotifications($userid){

        $result = $this->createQueryBuilder('n')
            ->select('n')
            ->where('n.userId = :userid')
            ->setParameter('userid', $userid)
            ->andWhere('n.viewed = 0')
            ->orderBy('n.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

}

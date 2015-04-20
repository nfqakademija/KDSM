<?php

namespace KDSM\APIBundle\Entity;

use Doctrine\ORM\EntityRepository;
use DateInterval;

/**
 * TableEventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TableEventRepository extends EntityRepository
{
//    public function getLatestEventId(){
//        $query = $this->createQueryBuilder('tb');
//        $query->select('MAX(tb.eventId)');
//        if($query->getQuery()->getResult()[0][1])
//            return $query->getQuery()->getResult()[0][1];
//        else
//            return 1;
//    }

    public function getLatestTableEvent(){
        $result = null;
        $query = $this->createQueryBuilder('tb');
        $query->select('tb')->orderBy('tb.id', 'desc')->setMaxResults(1);
        if($query->getQuery()->getResult())
            $result = $query->getQuery()->getResult()[0];
        return $result;
    }

    public function getShakeCountAtPeriod($timestamp, $period){

        $query = $this->createQueryBuilder('tb');
        $query->select('COUNT(tb.eventId)')
            ->where('tb.type = ?1')
            ->andWhere('tb.timesec >= ?2')
            ->andWhere('tb.timesec <= ?3');

        $query->setParameters(array(1 => 'TableShake', 2 => date('Y-m-d H:i:s', $timestamp-$period), 3 => date('Y-m-d H:i:s', $timestamp)));

        if($query->getQuery()->getResult()[0][1])
            return $query->getQuery()->getResult()[0][1];
        else
            return 0;
    }

//    todo unused atm
//    public function getEventsFromId($id){
//        $query = $this->createQueryBuilder('tb');
//        $query->select()
//            ->where('tb.id >= ?1');
//        $query->setParameters(array(1 => $id));
//        return $query->getQuery()->getResult();
//    }

    public function getGoalEventsFromId($id){
        $query = $this->createQueryBuilder('tb');
        $query->select()
            ->where('tb.id > ?1')
//            ->andWhere('tb.id< ?3')
            ->andWhere('tb.type = ?2')
            ->orderBy('tb.eventId', 'ASC');
        $query->setParameters(array(1 => $id, 2 => 'AutoGoal'/*, 3 => 3968*/));
        return $query->getQuery()->getResult();
    }
// todo unused anymore
//    public function getSwipesOnDateTime($timestamp){
//        $query = $this->createQueryBuilder('tb');
//        $query->select()
//            ->where('tb.timesec >= ?1')
//            ->andWhere('tb.timesec <= ?2')
//            ->andWhere('tb.type = ?3');
//        $query->setParameters(array(1 => date('Y-m-d H:i:s', strtotime('-2 minutes',$timestamp)), 2 => date('Y-m-d H:i:s', $timestamp), 3 => 'CardSwipe'));
//        return $query->getQuery()->getResult();
//    }

    public function persistObject($newEvent){
        $this->getEntityManager()->persist($newEvent);
        $this->getEntityManager()->flush();
    }
}

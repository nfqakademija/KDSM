<?php

namespace APIBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TableEventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TableEventRepository extends EntityRepository
{
    public function getLatestEvent(){
        $query = $this->createQueryBuilder('tb');
        $query->select('MAX(tb.eventId)');
        if($query->getQuery()->getResult()[0][1])
            return $query->getQuery()->getResult()[0][1];
        else
            return 1;
    }

    public function persistObject($newEvent){
        $this->getEntityManager()->persist($newEvent);
        $this->getEntityManager()->flush();
    }
}

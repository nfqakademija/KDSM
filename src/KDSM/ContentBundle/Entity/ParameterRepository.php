<?php

namespace KDSM\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ParameterRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ParameterRepository extends EntityRepository

{
    /*
    public function getAllParameters(){
        $query = $this->createQueryBuilder('p')
            ->groupBy('p.parameterName')
            ->getQuery();
        $results = $query->getResult();
        return $results;
    }*/

    public function getParameterValueByName($name){
        $value = $this->findOneBy(
            array('parameterName' => $name),
            array('id' => 'DESC'));
        return $value->getParameterValue();
    }

    public function getAllParameters(){
        $sql = 'SELECT p1.* FROM parameter p1 LEFT JOIN parameter p2 ON (p1.parameter_name = p2.parameter_name AND p1.id < p2.id) WHERE p2.id IS NULL';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        return $results;
    }
}

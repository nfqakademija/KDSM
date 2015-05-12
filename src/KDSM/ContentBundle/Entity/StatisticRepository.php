<?php

namespace KDSM\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * StatisticRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StatisticRepository extends EntityRepository
{
    public function addStatistic($statistic_id, $stats){
        $stat = new Statistic();
        $stat->setStatisticId($statistic_id);
        $stats_json = json_encode($stats);
        $stat->setStats($stats_json);

        $em = $this->getEntityManager();
        $em->persist($stat);
        $em->flush();
    }

    public function getAllStatistics(){
        $sql = 'SELECT s1.* FROM Statistic s1 LEFT JOIN Statistic s2 ON (s1.statistic_id = s2.statistic_id AND s1.id < s2.id) WHERE s2.id IS NULL ORDER BY s1.statistic_id';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        return $results;
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: Martynas
 * Date: 5/11/2015
 * Time: 6:04 PM
 */

namespace KDSM\ContentBundle\Services\Statistics;

use Doctrine\ORM\EntityManager;

class StatisticsService {

    private $rep;

    public function __construct(EntityManager $entityManager){
        $em = $entityManager;
        $this->rep = $em->getRepository('KDSMContentBundle:Statistic');
    }

    public function update(){
        $this->rep->addStatistic(1, array('foo'=>'bar'));
    }

}
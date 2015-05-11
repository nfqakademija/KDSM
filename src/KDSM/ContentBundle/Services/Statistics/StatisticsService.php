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
    private $tableEventRep;

    public function __construct(EntityManager $entityManager){
        $em = $entityManager;
        $this->rep = $em->getRepository('KDSMContentBundle:Statistic');
        $this->tableEventRep= $em->getRepository('KDSMAPIBundle:TableEvent');
    }

    public function update(){
        $goals = $this->tableEventRep->getGoalEventsFromId(0);
        $count = 0;

        foreach($goals as $goal){
            if(strpos($goal->getData(), '0') !== false){
                $count++;
            }
        }

        $percentage0 = ($count/sizeof($goals))*100;
        $percentage1 = 100 - $percentage0;

        $this->rep->addStatistic(1, array('0'=>$percentage0, '1'=>$percentage1));
    }

}
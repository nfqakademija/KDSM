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

        $weekarray = array_fill(0, 7, 0);
        $hoursarray = array_fill(0, 24, 0);

        foreach($goals as $goal){
            if(strpos($goal->getData(), '0') !== false){
                $count++;
            }

            $weekarray[$goal->getTimeSec()->format('w')]++;

            $hoursarray[intval($goal->getTimeSec()->format('H'))]++;
        }

        $percentage0 = ($count/sizeof($goals))*100;
        $percentage1 = 100 - $percentage0;

        for($i=0; $i<sizeof($weekarray); ++$i){
            $weekarray[$i] = ($weekarray[$i]/sizeof($goals))*100;
        }

        for($i=0; $i<sizeof($hoursarray); ++$i){
            $hoursarray[$i] = ($hoursarray[$i]/sizeof($goals))*100;
        }

        $this->rep->addStatistic(1, array('0'=>$percentage0, '1'=>$percentage1));
        $this->rep->addStatistic(2, $weekarray);
        $this->rep->addStatistic(3, $hoursarray);
    }

    public function getStatistics(){
        $res = $this->rep->getAllStatistics();
        for($i = 0; $i < sizeof($res); ++$i){
            $res[$i] = json_decode($res[$i]['stats']);
        }
        return $res;
    }

}
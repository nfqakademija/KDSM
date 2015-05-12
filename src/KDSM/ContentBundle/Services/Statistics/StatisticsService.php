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

        $sides = array_fill(0, 2, 0); // komandos imustu golu statistika
        $weekarray = array_fill(0, 7, 0);
        $hoursarray = array_fill(0, 24, 0);
        $weeksides = array_fill(0, 7, array_fill(0, 2, 0));

        foreach($goals as $goal){
            $week = $goal->getTimeSec()->format('w');

            if(strpos($goal->getData(), '0') !== false){
                $sides[0]++;
                $weeksides[$week][0]++;
            }else{
                $weeksides[$week][1]++;
            }

            $weekarray[$week]++;

            $hoursarray[intval($goal->getTimeSec()->format('H'))]++;
        }

        $sides[0] = ($sides[0]/sizeof($goals))*100; // skaiciuoja procentus kiek viena komanda imusa
        $sides[1] = 100 - $sides[0];

        for($i=0; $i<sizeof($weekarray); ++$i){ // skaiciuoja procentus kiek zaidziama per sava
            $weekarray[$i] = ($weekarray[$i]/sizeof($goals))*100;
        }

        for($i=0; $i<sizeof($hoursarray); ++$i){ // skaiciuoja procentus kiek zaidziama per diena
            $hoursarray[$i] = ($hoursarray[$i]/sizeof($goals))*100;
        }

        for($i=0; $i < 7; $i++){
            $goalsum = $weeksides[$i][0] + $weeksides[$i][1];
            $weeksides[$i][0] = ($weeksides[$i][0]/$goalsum)*100;
            $weeksides[$i][1] = 100 - $weeksides[$i][0];
        }


        $this->rep->addStatistic(1, $sides);
        $this->rep->addStatistic(2, $weekarray);
        $this->rep->addStatistic(3, $hoursarray);
        $this->rep->addStatistic(4, $weeksides);
    }

    public function getStatistics(){
        $res = $this->rep->getAllStatistics();
        for($i = 0; $i < sizeof($res); ++$i){
            $res[$i] = json_decode($res[$i]['stats']);
        }
        return $res;
    }

}
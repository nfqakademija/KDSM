<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/15/2015
 * Time: 3:24 PM
 */

namespace KDSM\ContentBundle\Services\LiveScore;

use Doctrine\ORM\EntityManager;

use KDSM\APIBundle\Entity\TableEvent;
use KDSM\ContentBundle\Entity\LiveScore;
use KDSM\ContentBundle\Services\Statistics;
use KDSM\ContentBundle\Services\Statistics\BusyCheck;


/**
 * Class LiveScoreManager
 * @package KDSM\ContentBundle\Services\LiveScore
 */
class LiveScoreManager{

    /**
     * @var LiveScore
     */
    protected $liveScore;

    /**
     * @var BusyCheck
     */
    protected $busyCheck;

    /**
     * @var \KDSM\APIBundle\Entity\TableEventRepository
     */
    protected $rep;

    /**
     * @param LiveScore $liveScore
     * @param BusyCheck $busyCheck
     * @param EntityManager $entityManager
     */
    public function __construct(LiveScore $liveScore, BusyCheck $busyCheck, EntityManager $entityManager){
        $this->liveScore = $liveScore;
        $this->busyCheck = $busyCheck;
        $this->em = $entityManager;
        $this->rep = $this->em->getRepository('KDSMAPIBundle:TableEvent');
    }

    /**
     *
     */
    public function getTableStatus($checkDateTime = '2014-10-06 09:02:00')
    {
        $status = $this->busyCheck->busyCheck($checkDateTime);

        if($status == 'free'){
            $response['status'] = $status;
        }
        else if ($status == 'busy'){
            $response['status'] = $status;
            $response['score'] = $this->readEvents($checkDateTime);

        }
        else
            $response['status'] = 'error';
        return $response;

    }

    public function readEvents($checkDateTime){
        $score = ['white' => 0, 'black' => 0];
        $getResults = true;
        $j = 0;
        $timestamp = strtotime($checkDateTime);
        while($getResults){
            if($this->busyCheck->busyCheck(date('Y-m-d H:i:s', $timestamp)) == 'busy') {
                $events = $this->rep->getEventsOnDateTime($timestamp);
                foreach ($events as  $event){
                    if (is_object($event) && $event instanceof TableEvent)
                        if($event->getType() == 'AutoGoal')
                            if(json_decode($event->getData())->team == 1 && !in_array(10, $score))
                                $score['white']++;
                            else $score['black']++;
                }
                $timestamp = strtotime('-1 minute', $timestamp);
                if(in_array(10, $score)) {
                    $getResults = false;
                }
            }
            else {
                $getResults = false;
            }

        }
        return $score;
    }

}
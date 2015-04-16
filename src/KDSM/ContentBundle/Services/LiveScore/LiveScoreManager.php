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
            $response['tableStatus'] = $status;
        }
        else if ($status == 'busy'){
            $response['tableStatus'] = $status;
            $response['tableData'] = $this->readEvents($checkDateTime);
//            $response
        }
        else
            $response['tableStatus'] = 'error';
        return $response;

    }


    /*
     * gets table events in 1 minute intervals from the given datetime. Counts goals until 10 on one side or
     * until table free status is reached.
     * If a cardswipe is encountered before reaching 10 goals on either side, it is treated as newgame flag and score is
     * immediately displayed.
     * Afterwards it continues until table free or first cardswipe event. It then checks for other cardswipe events
     * within 2 minute interval (not affected by able free anymore)
     */
    public function readEvents($checkDateTime){
        $table =['score' => ['white' => 0, 'black' => 0],'players' => [1 => 0, 2 => 0, 3 => 0, 4 => 0]];
        $getResults = true;
        $timestamp = strtotime($checkDateTime);
        while($getResults){
            if($this->busyCheck->busyCheck(date('Y-m-d H:i:s', $timestamp)) == 'busy') {
                $events = $this->rep->getEventsOnDateTime($timestamp);
                foreach ($events as  $event){
                    if (is_object($event) && $event instanceof TableEvent) {
                        if ($event->getType() == 'AutoGoal')
                            if (json_decode($event->getData())->team == 1 && !in_array(10, $table['score']))
                                $table['score']['white']++;
                            else $table['score']['black']++;
                        if ($event->getType() == 'CardSwipe'){
                            $getResults = false; // cardswipe resets the game score counter
                            break; //stops further result processing
                        }
                    }
                }
                $timestamp = strtotime('-1 minute', $timestamp);
                if(in_array(10, $table['score'])) {
                    $getResults = false;
                }
            }
            else {
                $getResults = false;
            }

        }
        return $table;
    }

}
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

    protected $table;

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
            $this->table =['score' => ['white' => 0, 'black' => 0],'players' => [/*should be set by frontend*/]];
            $response['tableStatus'] = $status;
            if($this->readEvents($checkDateTime))
                $response['tableData'] = $this->table;
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
     **/
    /*
     * ISSUES:
     * TODO only searches for players 2 mins after game ends.
     * TODO Further on later, when frontend will provide player ID's it will only check until that cardswipe.
     * TODO Currently players are overwritten by a swipe event
     * TODO Same player can play on multiple positions
     *
     */
    private function readEvents($checkDateTime){
        $getResults = true;
        $getPlayers = false;
        $timestamp = strtotime($checkDateTime);
        while($getResults){
            if($this->busyCheck->busyCheck(date('Y-m-d H:i:s', $timestamp)) == 'busy') {
                //get 1 minutes worth of events
                $events = $this->rep->getEventsOnDateTime($timestamp);
                foreach($events as  $event){
                    if(is_object($event) && $event instanceof TableEvent) {
                        //scan for goals. break on card swipe if 10 goals are not reached
                        //if 10 goals are reached, process and count card swipes.
                        if($event->getType() == 'AutoGoal' && !in_array(10, $this->table['score']))
                            if(json_decode($event->getData())->team == 1)
                                $this->table['score']['white']++;
                            else $this->table['score']['black']++;
//                        if($event->getType() == 'CardSwipe'){
//                            if(!in_array(10, $this->table['score'])) {
//                                $getResults = false; // cardswipe resets the game score counter
//                                break; //stops further result processing
//                            }
//                            else{
//                                $this->addPlayer($event);
//                                $getPlayers = true;
//                            }
//                        }
                    }
                }
                if($getResults) $timestamp = strtotime('-1 minute', $timestamp); //if goal scan is over do not advance the timestamp
                if(in_array(10, $this->table['score'])) {$getResults = false; $getPlayers = true;} //if game is over stop the goal scan
            }
            else {
                $getResults = false; //if table status = free
            }

        }
        if($getPlayers){
            $events = $this->rep->getSwipesOnDateTime($timestamp);
            foreach($events as  $event){
                if(is_object($event) && $event instanceof TableEvent) {
                    //add any other detected players
                    $this->addPlayer($event);
                }
            }
        }
        return true;
    }

    private function addPlayer($event){
        if(is_object($event) && $event instanceof TableEvent) {
            $playerId = $playerId = json_decode($event->getData())->team == 0 ? 1 +
                json_decode($event->getData())->player : 3 + json_decode($event->getData())->player;
            $this->table['players'][$playerId] = ['id' => json_decode($event->getData())->card_id];
        }
}

}
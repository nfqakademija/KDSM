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
use KDSM\ContentBundle\Services\Redis\CacheManager;
use KDSM\ContentBundle\Services\Statistics;
use KDSM\ContentBundle\Services\Statistics\BusyCheck;

/**
 * Class LiveScoreManager
 * @package KDSM\ContentBundle\Services\LiveScore
 */
class LiveScoreManager
{

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
     * @var CacheManager
     */
    protected $cacheMan;

    /**
     * @param LiveScore $liveScore
     * @param BusyCheck $busyCheck
     * @param EntityManager $entityManager
     * @param CacheManager $cacheMan
     */
    public function __construct(
        LiveScore $liveScore,
        BusyCheck $busyCheck,
        EntityManager $entityManager,
        CacheManager $cacheMan
    ) {
        $this->liveScore = $liveScore;
        $this->busyCheck = $busyCheck;
        $this->em = $entityManager;
        $this->rep = $this->em->getRepository('KDSMAPIBundle:TableEvent');
        $this->cacheMan = $cacheMan;
    }


    /**
     *
     */
    public function getTableStatus(/*$checkDateTime = '2014-10-06 09:02:00'*/)
    {
        //todo set to now() at live
//        $checkDateTime = strtotime('2014-10-07 09:05:00');
        $checkDateTime = strtotime('now');
        $status = $this->busyCheck->busyCheck($checkDateTime);
//        echo date('Y-m-d H:i:s', $checkDateTime);
//
//        $this->cacheMan->setLatestCheckedTableGoalId(3905);
        $this->getSwipes();
        $this->cacheMan->setLatestCheckedTableSwipeId($this->rep->getLatestId());
        if ($status == 'free') {
            $this->cacheMan->resetScoreCache();
            $this->cacheMan->setLatestCheckedTableGoalId($this->rep->getLatestId());
        } else {
            if ($status == 'busy') {
                $this->readEvents();
            } else {
                $status = 'error';
            }
        }
        $this->cacheMan->setTableStatusCache($status);
        return $status;
    }


    /*
     * gets latest table events. Counts result until one team wins.
     *
     * Gets/writes current result to Redis. Tracking is based on the event ID's in the database.
     * Online check is done counting shake events during last minute.
     *
     *  As this event should be ran several times during one
     * BusyCheck interval, the latter should filter out table busy/free stuff
     **/

    private function readEvents()
    {
        $table = $this->cacheMan->getScoreCache(); //gets latest result
        if (in_array(10, $table['score']))//reset to 0 if latest game ended with a score of 10
        {
            $table = $this->cacheMan->resetScoreCache();
        }
        $events = $this->rep->getGoalEventsFromId($this->cacheMan->getLatestCheckedTableGoalId());
        foreach ($events as $event) {
            if (is_object($event) && $event instanceof TableEvent) {
                if (json_decode($event->getData())->team == 1) {
                    $table['score']['black']++;
                } else {
                    $table['score']['white']++;
                }
                if (in_array(10, $table['score'])) {
                    break;
                }
            }
        }
        //cache up  stuff
        if (isset($event)) {
            $this->cacheMan->setLatestCheckedTableGoalId($event->getId());
            $this->cacheMan->setScoreCache($table['score']);
        }
        //cleanup
        unset($events);
        unset($event);

        return true;
    }

    private function getSwipes()
    {
        $position = 0;
        $swipes = $this->rep->getSwipeEventsFromId($this->cacheMan->getLatestCheckedTableSwipeId());
        foreach ($swipes as $swipe) {
            if (is_object($swipe) && $swipe instanceof TableEvent) {
                if (json_decode($swipe->getData())->team == 0) {
                    json_decode($swipe->getData())->player == 0 ? $position = 1 : $position = 2;
                }
                if (json_decode($swipe->getData())->team == 1) {
                    json_decode($swipe->getData())->player == 0 ? $position = 3 : $position = 4;
                }
                $this->cacheMan->setPlayerCache($position, json_decode($swipe->getData())->card_id);
            }
        }
        if(isset($event))
        {
            $this->cacheMan->setLatestCheckedTableSwipeId($event->getId());
            //cleanup
            unset($events);
            unset($event);
        }

        return true;
    }
}
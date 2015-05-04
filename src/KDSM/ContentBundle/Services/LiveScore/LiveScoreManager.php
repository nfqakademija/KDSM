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
                echo $event->getId() . "<br\n>";
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
}
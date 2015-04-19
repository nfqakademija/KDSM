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
use KDSM\ContentBundle\Services\Redis\CacheManager;

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
     * @var CacheManager
     */
    protected $cacheMan;

    protected $table;

    /**
     * @param LiveScore $liveScore
     * @param BusyCheck $busyCheck
     * @param EntityManager $entityManager
     * @param CacheManager $cacheMan
     */
    public function __construct(LiveScore $liveScore, BusyCheck $busyCheck, EntityManager $entityManager, CacheManager $cacheMan){
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
        $checkDateTime = strtotime('2014-10-06 09:05:00');
        $status = $this->busyCheck->busyCheck($checkDateTime);

        if($status == 'free'){
            $response['tableStatus'] = $status;
            $this->cacheMan->resetScoreCache();
        }
        else if ($status == 'busy'){
            $response['tableStatus'] = $status;
            if($this->readEvents())
                $response['tableData'] = $this->table;
        }
        else
            $response['tableStatus'] = 'error';
        return $response;

    }


    /*
     * gets latest table events. Counts result until one team wins. As this event should be ran several times during one
     * BusyCheck interval, the latter should filter out table busy/free stuff
     **/

    private function readEvents(){
        $this->table = $this->cacheMan->getScoreCache(); //gets latest result
        if(in_array(10, $this->table['score']))//reset to 0 if latest game ended with a score of 10
            $this->table = $this->cacheMan->resetScoreCache();

        $events = $this->rep->getGoalEventsFromId($this->cacheMan->getLatestCheckedTableGoalId());

        foreach($events as $event){
            if(is_object($event) && $event instanceof TableEvent) {
                if (json_decode($event->getData())->team == 1)
                    $this->table['score']['white']++;
                else $this->table['score']['black']++;

                if (in_array(10, $this->table['score'])) {
                    //$this->setLatestCheckedTableGoalId($event->getId());
                    print_r($this->table['score']);
                    $this->cacheMan->setScoreCache($this->table['score']);
                    //cache up  stuff
                    break;
                }
            }
        }
        return true;
    }
}
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
use KDSM\ContentBundle\Services\QueueManager;
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
     * @var BusyCheck
     */
    protected $busyCheck;

    /**
     * @var \KDSM\APIBundle\Entity\TableEventRepository
     */
    protected $rep;


    /**
     * @var \KDSM\ContentBundle\Entity\QueueRepository
     */
    protected $queueRep;

    /**
     * @var QueueManager
     */
    protected $queueManager;

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
        BusyCheck $busyCheck,
        EntityManager $entityManager,
        CacheManager $cacheMan,
        QueueManager $queueManager
    ) {
        $this->busyCheck = $busyCheck;
        $this->em = $entityManager;
        $this->rep = $this->em->getRepository('KDSMAPIBundle:TableEvent');
        $this->queueRep = $this->em->getRepository('KDSMContentBundle:Queue');
        $this->cacheMan = $cacheMan;
        $this->queueManager = $queueManager;
    }


    /**
     *
     */
    public function getTableStatus()
    {
        $checkDateTime = strtotime('now');
        $status = $this->busyCheck->busyCheck($checkDateTime);

        $this->getSwipes();
        $this->cacheMan->setLatestCheckedTableSwipeId($this->rep->getLatestId());

        $this->liveQueueCheck();

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

        $events = $this->rep->getGoalEventsFromLastCheckedEvent($this->getLastCheckedGoalId);

        if ($events != null) {
            foreach ($events as $event) {
                if ($this->checkGoalsForWinner($table)) {//reset to 0 if latest game ended with a score of 10
                    $table = $this->cacheMan->resetScoreCache();
                }
                // todo: getTeam() -> tableEventParser
                if (json_decode($event->getData())->team == 1) {
                    $table['score']['black']++;
                } else {
                    $table['score']['white']++;
                }
                if ($this->checkGoalsForWinner($table)) {
                    break;
                }
            }
            //cache up  stuff
            $this->cacheMan->setLatestCheckedTableGoalId($event->getId());
            $this->cacheMan->setScoreCache($table['score']);
            //cleanup
            unset($events);
            unset($event);
        }
        return true;
    }

    private function checkGoalsForWinner($table)
    {
        return in_array(10, $table['score']) ? true : false;
    }

    private function getLastCheckedGoalId()
    {
        return $this->cacheMan->getLatestCheckedTableGoalId();
    }

    /**
     * @return bool
     */
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

    private function liveQueueCheck()
    {
        $activeQueueStatus = $this->cacheMan->getCurrentActiveQueueStatus();
        switch ($activeQueueStatus) {
            case 'queue_empty':
                if ($this->queueManager->setNextQueueAsActive()) {
                    $this->cacheMan->setActiveQueueStatus('waiting_for_swipe');
                }
                break;
            case 'waiting_for_swipe':
                $swipedPlayers = $this->cacheMan->getPlayerCache();

                $activeQueue = $this->getActiveQueue();//getActiveQueuePlayers();
                if ($activeQueue != null) {
                    $activeQueuePlayers = null;
                    foreach ($activeQueue->getUsersQueues() as $uq) {
                        if ($this->isUserSwiped($uq)) {
                            $activeQueuePlayers[] = (string)$uq->getUser()->getCardId();
                        }
                    }
//                    foreach ($swipedPlayers['players'] as $player) {
//                        if (in_array($player, $activeQueuePlayers)) {
                            $this->cacheMan->setActiveQueueStatus('ready_to_play');
//                        }
//                    }
                }
                break;
            case 'ready_to_play':
                $status = $this->cacheMan->getTableStatusCache();
                if ($this->cacheMan->getTableStatusCache() != 'free') {
                    $this->cacheMan->setActiveQueueStatus('playing');
                }
                break;
            case 'playing':
                if ($this->cacheMan->getTableStatusCache() == 'free') {
                    $this->queueManager->setActiveQueueAsExpired();
                    $this->cacheMan->setActiveQueueStatus('queue_empty');
                }
                break;
            default:
                $this->cacheMan->setActiveQueueStatus('queue_empty');
                break;
        }

        return false;
    }

    private function getActiveQueue()
    {
        return $this->queueRep->findOneBy(array('status' => 'active'));
    }

    private function isUserSwiped($uq)
    {
        ($uq->getUserStatusInQueue() == 'inviteAccepted' ||
            $uq->getUserStatusInQueue() == 'queueOwner') ? true : false;
    }
}
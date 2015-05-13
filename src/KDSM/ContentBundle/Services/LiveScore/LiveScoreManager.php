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
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->rep = $this->em->getRepository('KDSMAPIBundle:TableEvent');
        $this->queueRep = $this->em->getRepository('KDSMContentBundle:Queue');
    }

    /**
     * @param BusyCheck $busyCheck
     */
    public function setTableBusyCheck(BusyCheck $busyCheck)
    {
        $this->busyCheck = $busyCheck;
    }

    /**
     * @param CacheManager $cacheMan
     */
    public function setCacheManager(CacheManager $cacheMan)
    {
        $this->cacheMan = $cacheMan;
    }

    public function setQueueManager(QueueManager $queueManager)
    {
        $this->queueManager = $queueManager;
    }

    /**
     * @return null|string
     */
    public function getTableStatus()
    {
        $checkDateTime = strtotime('now');
        $busyStatus = $this->busyCheck->busyCheck($checkDateTime);

        $this->getSwipes();
        $this->cacheMan->setLatestCheckedTableSwipeId($this->rep->getLatestId());

        $this->liveQueueCheck();

        if ($busyStatus === null) {
            $tableStatus = 'error';
        } else if (!$busyStatus) {
            $this->cacheMan->resetScoreCache();
            $this->cacheMan->setLatestCheckedTableGoalId($this->rep->getLatestId());
            $tableStatus = 'free';
        } else if ($busyStatus) {
                $this->readEvents();
                $tableStatus = 'busy';
        }
        $this->cacheMan->setTableStatusCache($tableStatus);
        return $tableStatus;
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

        $events = $this->rep->getGoalEventsFromLastCheckedEvent($this->getLastCheckedGoalId());

        $eventParser = new TableEventParser();

        if ($events != null) {
            foreach ($events as $event) {
                if ($this->checkGoalsForWinner($table)) {//reset to 0 if latest game ended with a score of 10
                    $table = $this->cacheMan->resetScoreCache();
                }
                // todo: getTeam() -> tableEventParser
                $scorer = $eventParser->getWhoScoredTheGoal($event);
                if ($scorer) {
                    $table['score'][$scorer]++;
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
        if ($swipes != null) {
            $eventParser = new TableEventParser();
            foreach ($swipes as $swipe) {
                $position = $eventParser->getSwipePosition($swipe);
                if ($position) {
                    $this->cacheMan->setPlayerCache($position, json_decode($swipe->getData())->card_id);
                }
            }
            $this->cacheMan->setLatestCheckedTableSwipeId($swipe->getId());
            //cleanup
            unset($swipes);
            unset($swipe);
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
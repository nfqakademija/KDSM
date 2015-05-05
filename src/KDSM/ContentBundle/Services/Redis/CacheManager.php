<?php
namespace KDSM\ContentBundle\Services\Redis;

/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/19/2015
 * Time: 12:02 PM
 */

class CacheManager
{

    private $redis;
    private $em;
    private $rep;

    public function __construct($redis, $entityManager)
    {
        $this->redis = $redis;
        $this->em = $entityManager;
        $this->rep = $this->em->getRepository('KDSMAPIBundle:TableEvent');
    }

    public function getLatestCheckedTableGoalId()
    {
        if (!$this->redis->exists('liveScore:lastCheckGoalId')) {
            $eventId = $this->rep->getLatestId();
        } else {
            $eventId = $this->redis->get('liveScore:lastCheckGoalId');
        }

        return $eventId;
    }

    public function setLatestCheckedTableGoalId($id)
    {
        $this->redis->set('liveScore:lastCheckGoalId', $id);
    }

    public function getLatestCheckedTableSwipeId()
    {
        if (!$this->redis->exists('liveScore:lastCheckSwipeId')) {
            $eventId = $this->rep->getLatestId();
        } else {
            $eventId = $this->redis->get('liveScore:lastCheckSwipeId');
        }

        return $eventId;
    }

    public function setLatestCheckedTableSwipeId($id)
    {
        $this->redis->set('liveScore:lastCheckSwipeId', $id);
    }

    public function resetScoreCache()
    {
        $this->redis->hset('table:LiveScore', 'scoreWhite', 0);
        $this->redis->hset('table:LiveScore', 'scoreBlack', 0);

        return ['score' => ['white' => 0, 'black' => 0]];
    }

    public function getScoreCache()
    {
        return [
            'score' => [
                'white' => $this->redis->hget('table:LiveScore', 'scoreWhite'),
                'black' => $this->redis->hget('table:LiveScore', 'scoreBlack')
            ]
        ];
    }

    public function setScoreCache($score)
    {
        $this->redis->hset('table:LiveScore', 'scoreWhite', $score['white']);
        $this->redis->hset('table:LiveScore', 'scoreBlack', $score['black']);
    }

    public function resetPlayerCache()
    {
        $this->redis->hset('table:LiveScore', 'player1', '');
        $this->redis->hset('table:LiveScore', 'player2', '');
        $this->redis->hset('table:LiveScore', 'player3', '');
        $this->redis->hset('table:LiveScore', 'player4', '');
    }

    public function getPlayerCache()
    {
        return [
            'players' => [
                'player1' => $this->redis->hget('table:LiveScore', 'player1'),
                'player2' => $this->redis->hget('table:LiveScore', 'player2'),
                'player3' => $this->redis->hget('table:LiveScore', 'player3'),
                'player4' => $this->redis->hget('table:LiveScore', 'player4')
            ]
        ];
    }

    public function setPlayerCache($playerPosition, $playerId)
    {
        $this->redis->hset('table:LiveScore', 'player'.$playerPosition,$playerId);
    }

    public function getTableStatusCache()
    {
        return $this->redis->hget('table:LiveScore', 'tableStatus');
    }

    public function setTableStatusCache($status)
    {
        return $this->redis->hset('table:LiveScore', 'tableStatus', $status);
    }


}
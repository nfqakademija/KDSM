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

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    public function getLatestCheckedTableGoalId()
    {
        if (!$this->redis->exists('liveScore:lastCheckId')) {
            $latestEvent = $this->rep->getLatestId();
            //todo uncomment above line when testing on live data
//            $latestEvent = 3903;
            $this->redis->set('liveScore:lastCheckId', $latestEvent);
            $eventId = $latestEvent;
        } else {
            $eventId = $this->redis->get('liveScore:lastCheckId');
        }

        return $eventId;
    }

    public function setLatestCheckedTableGoalId($id)
    {
        $this->redis->set('liveScore:lastCheckId', $id);
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

    public function getTableStatusCache()
    {
        return $this->redis->hget('table:LiveScore', 'tableStatus');
    }

    public function setTableStatusCache($status)
    {
        return $this->redis->hset('table:LiveScore', 'tableStatus', $status);
    }


}
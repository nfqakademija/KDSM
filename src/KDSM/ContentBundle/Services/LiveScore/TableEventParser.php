<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 5/13/2015
 * Time: 11:16 AM
 */

namespace KDSM\ContentBundle\Services\LiveScore;

class TableEventParser
{
    /**
     * @param $event
     * @return null|string
     */
    public function getWhoScoredTheGoal($event)
    {
        $whoScored = null;
        if (json_decode($event->getData())->team == 1) {
            $whoScored = 'black';
        } else {
            $whoScored = 'white';
        }
        return $whoScored;
    }

    /**
     * @param $event
     * @return int|null
     */
    public function getSwipePosition($event)
    {
        $position = null;
        if (json_decode($event->getData())->team == 0) {
            json_decode($event->getData())->player == 0 ? $position = 1 : $position = 2;
        }
        if (json_decode($event->getData())->team == 1) {
            json_decode($event->getData())->player == 0 ? $position = 3 : $position = 4;
        }

        return $position;
    }
}
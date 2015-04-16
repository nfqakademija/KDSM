<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/15/2015
 * Time: 3:24 PM
 */

namespace KDSM\ContentBundle\Services\LiveScore;

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
     * @param LiveScore $liveScore
     * @param BusyCheck $busyCheck
     */
    public function __construct(LiveScore $liveScore, BusyCheck $busyCheck){
        $this->liveScore = $liveScore;
        $this->busyCheck = $busyCheck;
    }

    /**
     *
     */
    public function getTableStatus($checkDateTime = '2014-10-06 08:59:43')
    {
        $status = $this->busyCheck->busyCheck($checkDateTime);

        if($status == 'free'){
            $response['status'] = $status;
        }
        else if ($status == 'busy'){
            $response['status'] = $status;

        }
        else
            $response['status'] = 'error';
        return $response;

    }

//    public function(){
//
//    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/15/2015
 * Time: 3:24 PM
 */

namespace KDSM\ContentBundle\Services\LiveScore;

use KDSM\ContentBundle\Entity\LiveScore;

class LiveScoreManager {

    /*
     * @var \Entity\LiveScore
     */
    private $liveScore;

    public function __construct(){
        $this->liveScore = new LiveScore();
    }
//    public function(){
//
//    }

}
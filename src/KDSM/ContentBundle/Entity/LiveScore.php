<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/15/2015
 * Time: 3:48 PM
 */

namespace KDSM\ContentBundle\Entity;

//use KDSM\ContentBundle\


class LiveScore {

    private $status;
    private $players;
    private $score;

    public function __construct(){
        $this->status = 'unknown';

    }
}
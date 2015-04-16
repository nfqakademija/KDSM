<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/15/2015
 * Time: 3:48 PM
 */

namespace KDSM\ContentBundle\Entity;


class LiveScore {

    private $status;
    private $players;
    private $score;

    public function __construct(){
        $this->status = 'unknown';
        $this->players['player1'] = new User;
        $this->players['player2'] = new User;
        $this->players['player3'] = new User;
        $this->players['player4'] = new User;
        $this->score['white'] = 0;
        $this->score['black'] = 0;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param mixed $players
     */
    public function setPlayers($players)
    {
        $this->players = $players;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }
}
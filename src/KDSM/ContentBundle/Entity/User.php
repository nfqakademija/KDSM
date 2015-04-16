<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/9/2015
 * Time: 2:57 PM
 */

namespace KDSM\ContentBundle\Entity;


use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;


/**
 * User
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * @var integer
     */
    protected $cardId;

    /*
     * @var string
     */
    protected $profilePicturePath;

    /*
     * @var integer
     */
    protected $skillLevel;

    /**
     * @var \DateTime
     */
    protected $lastPlayed;

    /*
     * @var Integer
     */
    protected $wins;

    /*
     * @var Integer
     */
    protected $losses;


    /**
     * Set cardId
     *
     * @param integer $cardId
     * @return User
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;

        return $this;
    }

    /**
     * Get cardId
     *
     * @return integer 
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * Set profilePicturePath
     *
     * @param string $profilePicturePath
     * @return User
     */
    public function setProfilePicturePath($profilePicturePath)
    {
        $this->profilePicturePath = $profilePicturePath;

        return $this;
    }

    /**
     * Get profilePicturePath
     *
     * @return string 
     */
    public function getProfilePicturePath()
    {
        return $this->profilePicturePath;
    }

    /**
     * Set skillLevel
     *
     * @param integer $skillLevel
     * @return User
     */
    public function setSkillLevel($skillLevel)
    {
        $this->skillLevel = $skillLevel;

        return $this;
    }

    /**
     * Get skillLevel
     *
     * @return integer 
     */
    public function getSkillLevel()
    {
        return $this->skillLevel;
    }

    /**
     * Set lastPlayed
     *
     * @param \DateTime $lastPlayed
     * @return User
     */
    public function setLastPlayed($lastPlayed)
    {
        $this->lastPlayed = $lastPlayed;

        return $this;
    }

    /**
     * Get lastPlayed
     *
     * @return \DateTime 
     */
    public function getLastPlayed()
    {
        return $this->lastPlayed;
    }

    /**
     * Set wins
     *
     * @param integer $wins
     * @return User
     */
    public function setWins($wins)
    {
        $this->wins = $wins;

        return $this;
    }

    /**
     * Get wins
     *
     * @return integer 
     */
    public function getWins()
    {
        return $this->wins;
    }

    /**
     * Set losses
     *
     * @param integer $losses
     * @return User
     */
    public function setLosses($losses)
    {
        $this->losses = $losses;

        return $this;
    }

    /**
     * Get losses
     *
     * @return integer 
     */
    public function getLosses()
    {
        return $this->losses;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/23/2015
 * Time: 9:18 AM
 */

namespace KDSM\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Queue
 */
class Queue
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var boolean
     */
    private $isFourPlayers;

    /**
     * @var \DateTime
     */
    private $reservationDateTime;

    /**
     * @var string
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $usersQueues;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usersQueues = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isFourPlayers
     *
     * @param boolean $isFourPlayers
     * @return Queue
     */
    public function setIsFourPlayers($isFourPlayers)
    {
        $this->isFourPlayers = $isFourPlayers;

        return $this;
    }

    /**
     * Get isFourPlayers
     *
     * @return boolean 
     */
    public function getIsFourPlayers()
    {
        return $this->isFourPlayers;
    }

    /**
     * Set reservationDateTime
     *
     * @param \DateTime $reservationDateTime
     * @return Queue
     */
    public function setReservationDateTime($reservationDateTime)
    {
        $this->reservationDateTime = $reservationDateTime;

        return $this;
    }

    /**
     * Get reservationDateTime
     *
     * @return \DateTime 
     */
    public function getReservationDateTime()
    {
        return $this->reservationDateTime;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Queue
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add usersQueues
     *
     * @param \KDSM\ContentBundle\Entity\UsersQueues $usersQueues
     * @return Queue
     */
    public function addUsersQueue(\KDSM\ContentBundle\Entity\UsersQueues $usersQueues)
    {
        $this->usersQueues[] = $usersQueues;

        return $this;
    }

    /**
     * Remove usersQueues
     *
     * @param \KDSM\ContentBundle\Entity\UsersQueues $usersQueues
     */
    public function removeUsersQueue(\KDSM\ContentBundle\Entity\UsersQueues $usersQueues)
    {
        $this->usersQueues->removeElement($usersQueues);
    }

    /**
     * Get usersQueues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsersQueues()
    {
        return $this->usersQueues;
    }
}

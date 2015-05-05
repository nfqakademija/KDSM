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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    private $status;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add users
     *
     * @param \KDSM\ContentBundle\Entity\User $users
     * @return Queue
     */
    public function addUser(\KDSM\ContentBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \KDSM\ContentBundle\Entity\User $users
     */
    public function removeUser(\KDSM\ContentBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
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
}

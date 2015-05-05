<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 5/5/2015
 * Time: 6:36 PM
 */

namespace KDSM\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class UsersQueues {

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $queueId;

    /**
     * @var string
     */
    private $userStatusInQueue;

    /**
     * @var \KDSM\ContentBundle\Entity\User
     */
    private $user;


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
     * Set queueId
     *
     * @param integer $queueId
     * @return UsersQueues
     */
    public function setQueueId($queueId)
    {
        $this->queueId = $queueId;

        return $this;
    }

    /**
     * Get queueId
     *
     * @return integer 
     */
    public function getQueueId()
    {
        return $this->queueId;
    }

    /**
     * Set userStatusInQueue
     *
     * @param string $userStatusInQueue
     * @return UsersQueues
     */
    public function setUserStatusInQueue($userStatusInQueue)
    {
        $this->userStatusInQueue = $userStatusInQueue;

        return $this;
    }

    /**
     * Get userStatusInQueue
     *
     * @return string 
     */
    public function getUserStatusInQueue()
    {
        return $this->userStatusInQueue;
    }

    /**
     * Set user
     *
     * @param \KDSM\ContentBundle\Entity\User $user
     * @return UsersQueues
     */
    public function setUser(\KDSM\ContentBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \KDSM\ContentBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}

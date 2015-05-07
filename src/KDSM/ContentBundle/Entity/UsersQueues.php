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
     * @var string
     */
    private $userStatusInQueue;

    /**
     *
     * @var \KDSM\ContentBundle\Entity\User
     */
    private $user;

    /**
     * @ManyToOne(targetEntity="Queue", cascade={"persist", "remove"})
     * @var \KDSM\ContentBundle\Entity\Queue
     */
    private $queue;

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

    /**
     * Set queue
     *
     * @param \KDSM\ContentBundle\Entity\Queue $queue
     * @return UsersQueues
     */
    public function setQueue(\KDSM\ContentBundle\Entity\Queue $queue = null)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Get queue
     *
     * @return \KDSM\ContentBundle\Entity\Queue 
     */
    public function getQueue()
    {
        return $this->queue;
    }
}

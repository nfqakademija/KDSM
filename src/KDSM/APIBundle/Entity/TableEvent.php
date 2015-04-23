<?php

namespace KDSM\APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TableEvent
 */
class TableEvent
{
    /**
     * @var integer
     */
    private $id;

    /**
     *  $var integer
     */
    private $eventId;

    /**
     * @var \DateTime
     */
    private $timesec;

    /**
     * @var string
     */
    private $usec;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $data;

    /**
     * @var TableEventType
     */
    private $eventType;

    public function __construct()
    {
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
     * @return mixed
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param mixed $eventId
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Get timesec
     *
     * @return \DateTime
     */
    public function getTimesec()
    {
        return $this->timesec;
    }

    /**
     * Set timesec
     *
     * @param \DateTime $timesec
     * @return TableEvent
     */
    public function setTimesec($timesec)
    {
        $this->timesec = $timesec;

        return $this;
    }

    /**
     * Get usec
     *
     * @return string
     */
    public function getUsec()
    {
        return $this->usec;
    }

    /**
     * Set usec
     *
     * @param string $usec
     * @return TableEvent
     */
    public function setUsec($usec)
    {
        $this->usec = $usec;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return TableEvent
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return TableEvent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}

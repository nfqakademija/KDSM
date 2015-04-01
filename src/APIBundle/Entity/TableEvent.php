<?php

namespace APIBundle\Entity;

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
     * @var integer
     */
    private $typeId;

    /**
     * @var string
     */
    private $data;

    /**
     * @var TableEventType
     */
    private $eventType;

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
     * Get timesec
     *
     * @return \DateTime 
     */
    public function getTimesec()
    {
        return $this->timesec;
    }

    /**
     * Set usec
     *
     * @param string $usec
     * @return TableEvent
     */public function setUsec($usec)
    {
        $this->usec = $usec;

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
     * Set type
     *
     * @param integer $type
     * @return TableEvent
     */
    public function setTypeID($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getTypeID()
    {
        return $this->typeId;
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
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return TableEventType
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param TableEventType $eventType
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
    }

    public function __construct(){
        $this->eventType = new TableEventType();
    }
}

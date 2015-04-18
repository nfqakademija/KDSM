<?php

namespace KDSM\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parameter
 */
class Parameter
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $parameterName;

    /**
     * @var string
     */
    private $parameterValue;

    /**
     * @var \DateTime
     */
    private $timeChanged;


    public function __construct()
    {
        $this->timeChanged = new \DateTime();
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
     * Set parameterName
     *
     * @param string $parameterName
     * @return Parameter
     */
    public function setParameterName($parameterName)
    {
        $this->parameterName = $parameterName;

        return $this;
    }

    /**
     * Get parameterName
     *
     * @return string 
     */
    public function getParameterName()
    {
        return $this->parameterName;
    }

    /**
     * Set parameterValue
     *
     * @param string $parameterValue
     * @return Parameter
     */
    public function setParameterValue($parameterValue)
    {
        $this->parameterValue = $parameterValue;

        return $this;
    }

    /**
     * Get parameterValue
     *
     * @return string 
     */
    public function getParameterValue()
    {
        return $this->parameterValue;
    }

    /**
     * Set timeChanged
     *
     * @param \DateTime $timeChanged
     * @return Parameter
     */
    public function setTimeChanged($timeChanged)
    {
        $this->timeChanged = $timeChanged;

        return $this;
    }

    /**
     * Get timeChanged
     *
     * @return \DateTime 
     */
    public function getTimeChanged()
    {
        return $this->timeChanged;
    }
}

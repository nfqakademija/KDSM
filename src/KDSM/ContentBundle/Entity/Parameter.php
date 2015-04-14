<?php

namespace KDSM\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parameter
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Parameter
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="parameter_name", type="string", length=100)
     */
    private $parameterName;

    /**
     * @var string
     *
     * @ORM\Column(name="parameter_value", type="string", length=100)
     */
    private $parameterValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_changed", type="datetime")
     */
    private $timeChanged;


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

<?php

namespace KDSM\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statistic
 */
class Statistic
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $statisticId;

    /**
     * @var array
     */
    private $stats;


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
     * Set statisticId
     *
     * @param integer $statisticId
     * @return Statistic
     */
    public function setStatisticId($statisticId)
    {
        $this->statisticId = $statisticId;

        return $this;
    }

    /**
     * Get statisticId
     *
     * @return integer 
     */
    public function getStatisticId()
    {
        return $this->statisticId;
    }

    /**
     * Set stats
     *
     * @param array $stats
     * @return Statistic
     */
    public function setStats($stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * Get stats
     *
     * @return array 
     */
    public function getStats()
    {
        return $this->stats;
    }
}

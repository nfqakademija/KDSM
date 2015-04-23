<?php
/**
 * Service checks the shake count over last 2 minutes. if the count is below threshold ir returns false,
 * otherwise it returns true
 */

namespace KDSM\ContentBundle\Services\Statistics;

use Doctrine\ORM\EntityManager;

class BusyCheck
{

    /**
     * @var \KDSM\APIBundle\Entity\TableEventRepository
     */
    private $rep;
    private $busyThreshold;
    private $checkPeriod;

    public function __construct(EntityManager $entityManager, $threshold, $checkPeriod)
    {
        $this->em = $entityManager;
        $this->rep = $this->em->getRepository('KDSMAPIBundle:TableEvent');
        $this->busyThreshold = $threshold;
        $this->checkPeriod = $checkPeriod;
    }

    public function busyCheck($checkDateTime)
    {
        $shakesNow = $this->rep->getShakeCountAtPeriod($checkDateTime, $this->checkPeriod);
        $tableStatus = null;
        if ($shakesNow <= $this->busyThreshold) {
//            $shakesMinuteAgo = $this->getShakesPerMinute($checkDateTime-60);
//            if($shakesMinuteAgo <= $this->busyThreshold)
            $tableStatus = 'free';
        } else {
            $tableStatus = 'busy';
        }

        return $tableStatus;
    }
}
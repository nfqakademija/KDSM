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

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->rep = $this->em->getRepository('KDSMAPIBundle:TableEvent');


    }

    public function setTableBusyThreshold($threshold)
    {
        $this->busyThreshold = $threshold;
    }

    public function setTableCheckPeriod($checkPeriod)
    {
        $this->checkPeriod = $checkPeriod;
    }

    /**
     * @param $checkDateTime
     * @return bool|null
     */

    public function busyCheck($checkDateTime)
    {
        $shakesNow = $this->rep->getShakeCountAtPeriod($checkDateTime, $this->checkPeriod);
        $tableBusy = null;
        if ($shakesNow <= $this->busyThreshold) {
            $tableBusy = false;
        } else {
            $tableBusy = true;
        }

        return $tableBusy;
    }
}
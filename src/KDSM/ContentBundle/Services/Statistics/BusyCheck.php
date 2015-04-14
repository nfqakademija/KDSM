<?php
/**
 * Service checks the shake count over last 2 minutes. if the count is below threshold ir returns false,
 * otherwise it returns true
 */

namespace KDSM\ContentBundle\Services\Statistics;

use Doctrine\ORM\EntityManager;

class BusyCheck {

    /**
     * @var \KDSM\APIBundle\Entity\TableEventRepository
     */
    private $rep;
    private $busyThreshold;

    public function __construct(EntityManager $entityManager, $threshold){
        $this->em = $entityManager;
        $this->rep = $this->em->getRepository('KDSMAPIBundle:TableEvent');
        $this->busyThreshold = $threshold;
    }

    private function getShakesPerMinute($eventDateTime){
        return $this->rep->getShakeCountAtMinute($eventDateTime);
    }

    public function busyCheck($checkDateTime){
        $shakesNow = $this->getShakesPerMinute(strtotime($checkDateTime));
        if($shakesNow <= $this->busyThreshold) {
            $shakesMinuteAgo = $this->getShakesPerMinute(strtotime($checkDateTime)-60);
            if($shakesMinuteAgo <= $this->busyThreshold)
                return 'free';
        }
        return 'busy';
    }

    private function refreshAPIEvents(){

    }
}
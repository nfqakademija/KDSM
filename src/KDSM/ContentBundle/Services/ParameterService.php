<?php
/**
 * Created by PhpStorm.
 * User: Martynas
 * Date: 4/15/2015
 * Time: 8:49 PM
 */

namespace KDSM\ContentBundle\Services;

use Doctrine\ORM\EntityManager;

class ParameterService {

    protected $rep;

    public function __construct($entityManager){
        $this->em = $entityManager;
        $this->rep = $this->em->getRepository('KDSMContentBundle:Parameter');
    }


    public function getParameterValue($name){
        return $this->rep->getParameterValueByName($name);
    }
}
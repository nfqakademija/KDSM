<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/1/2015
 * Time: 7:03 PM
 */

namespace KDSM\APIBundle\Services;

use Doctrine\ORM\EntityManager;
use KDSM\APIBundle\Entity\TableEvent;
use KDSM\APIBundle\Services\fileIO\CsvIterator;


class DbManager {
    /**
     * @var CsvIterator
     */
    private $iterator;
    /**
     * @var \KDSM\APIBundle\Entity\TableEventRepository
     */
    private $rep;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
        $this->rep = $this->em->getRepository('KDSMAPIBundle:TableEvent');
    }

    public function insertObject($object){
        $newEvent = new TableEvent();
        $newEvent->setEventId($object['id']);
        $newEvent->setTimesec(new \DateTime(date("Y-m-d H:i:s", $object['timeSec'])));
        $newEvent->setUsec($object['usec']);
        $newEvent->setType($object['type']);
        $newEvent->setData($object['data']);
        $this->rep->persistObject($newEvent);
    }

    public function setIterator($iterator){
        $this->iterator = $iterator;
    }

    /**
     * @param $iterator
     */
    public function writeCsvToDb(\Iterator $iterator){
        $this->setIterator($iterator);
        while ($this->iterator->next()){
            $this->insertObject($iterator->current());
        }
    }

    public function writeJsonToDb($apiResponse){
        foreach($apiResponse['records'] as $object){
            $this->insertObject($object);
        }
        return sizeof($apiResponse['records']) == 100 ? true : false;
    }

    public function getLatest(Caller $apiCaller, $dumpAll){
        $isFullCall = $this->writeJsonToDb($apiCaller->callApi(100, $this->rep->getLatestEventId()));
        echo $this->rep->getLatestEventId() . "\n";
        if($dumpAll)
            while($isFullCall) {
                $isFullCall = $this->writeJsonToDb($apiCaller->callApi(100, $this->rep->getLatestEventId()));
                echo $this->rep->getLatestEventId() . "\n";
            }
    }
}
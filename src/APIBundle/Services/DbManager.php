<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/1/2015
 * Time: 7:03 PM
 */

namespace APIBundle\Services;

use Doctrine\ORM\EntityManager;
use APIBundle\Entity\TableEvent;
use APIBundle\Entity\TableEventType;
use APIBundle\Services\fileIO\CsvIterator;


class DbManager {
    /**
     * @var CsvIterator
     */
    private $iterator;
    /**
     * @var \APIBundle\Entity\TableEventRepository
     */
    private $rep;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
        $this->rep = $this->em->getRepository('APIBundle:TableEvent');
    }

    public function insertObject($object){
        $newEvent = new TableEvent();
        $newEvent->setEventId($object['id']);
        $newEvent->setTimesec(new \DateTime(date("Y-m-d H:i:s", $object['timeSec'])));
        $newEvent->setUsec($object['usec']);
        $newEvent->setTypeID($this->em->getRepository('APIBundle:TableEventType')->findBy(array('name' => $object['type']))[0]->getId());
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
        $isFullCall = $this->writeJsonToDb($apiCaller->callApi(100, $this->rep->getLatestEvent()));
        echo $this->rep->getLatestEvent() . "\n";
        if($dumpAll)
            while($isFullCall) {
                $isFullCall = $this->writeJsonToDb($apiCaller->callApi(100, $this->rep->getLatestEvent()));
                echo $this->rep->getLatestEvent() . "\n";
            }
    }
}
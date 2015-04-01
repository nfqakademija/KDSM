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


class DbManager {
    /**
     * @var CsvIterator
     */
    private $iterator;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }

    public function insertObject($object){
        $newEvent = new TableEvent();
        $newEvent->setEventId($object['id']);
        $newEvent->setTimesec(new \DateTime(date("Y-m-d H:i:s", $object['timesec'])));
        $newEvent->setUsec($object['usec']);
        $newEvent->setTypeID($this->em->getRepository('APIBundle:TableEventType')->findBy(array('name' => $object['type']))[0]->getId());
        $newEvent->setData($object['data']);
        $this->em->persist($newEvent);
        $this->em->flush();
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
}
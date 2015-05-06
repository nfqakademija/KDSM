<?php
/**
 * Created by PhpStorm.
 * User: Martynas
 * Date: 5/5/2015
 * Time: 4:50 PM
 */

namespace KDSM\ContentBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\Event;

class KDSMNotificationListener {

    protected $em;
    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    public function onNotificationCreate(Event $event){
        $rep = $this->em->getRepository('KDSMContentBundle:Notification');
        $rep->createNotification($event->getArgument('userid'), $event->getArgument('gameid'));
    }

}
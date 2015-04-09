<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/9/2015
 * Time: 11:39 AM
 */

namespace KDSM\APIBundle\Services\fileIO;

use Symfony\Component\EventDispatcher\Event;

class callerListener {

    public function onApiSuccessAction(Event $event = null){
        echo 'API Call Successful';
    }

}
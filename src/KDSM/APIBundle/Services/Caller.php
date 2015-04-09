<?php
namespace KDSM\APIBundle\Services;

use GuzzleHttp;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Caller {

    protected $url;
    protected $user;
    protected $password;
    protected $eventDispatcher;
    protected $listener;

    public function __construct($url, $user, $password){
        $this->url = $url;
        $this->user = $user;
        $this->password = $password;
        $this->eventDispatcher = new EventDispatcher();
        $this->listener = new CallerListener();
        $this->eventDispatcher->addListener('api.success.action', array($this->listener, 'onApiSuccessAction'));
    }

    public function callApi($count = 100, $startId = 1){
        $client = new GuzzleHttp\Client();
        try {
            $res = $client->get($this->url . '?rows=' . $count . '&from-id=' . $startId, ['auth' => [$this->user, $this->password]]);
        } catch(ConnectException $e){
            return false;
        }
        $this->eventDispatcher->dispatch('api.success.action');
        return $res->json();
    }

}
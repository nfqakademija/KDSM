<?php
namespace KDSM\APIBundle\Services;

use GuzzleHttp;
use GuzzleHttp\Exception\ConnectException;

class Caller {

    protected $url;
    protected $user;
    protected $password;
    protected $client;

    public function __construct($url, $user, $password, $client){
        $this->url = $url;
        $this->user = $user;
        $this->password = $password;
        $this->client = $client;
    }

    public function callApi($count = 100, $startId = 1){
        try {
            $res = $this->client->get($this->url,['query' => ['rows' => $count, 'from-id' => $startId], 'auth' => [$this->user, $this->password]]);
        } catch(ConnectException $e){
            return false;
        }
        return $res->json();
    }

}
<?php
namespace APIBundle\Services;

use GuzzleHttp;
use GuzzleHttp\Exception\ConnectException;

class APICaller {

    protected $url;
    protected $user;
    protected $password;

    public function __construct($url, $user, $password){
        $this->url = $url[0];
        $this->user = $user[0];
        $this->password = $password[0];
    }

    public function callApi($count = 100, $startId = 1){
        $client = new GuzzleHttp\Client();
        try {
            $res = $client->get($this->url . '?rows=' . $count . '&from-id=' . $startId, ['auth' => [$this->user, $this->password]]);
        } catch(ConnectException $e){
            return false;
        }
        return $res->json();
    }

}
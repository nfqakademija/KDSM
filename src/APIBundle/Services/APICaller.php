<?php
namespace APIBundle\Services;

use GuzzleHttp;
use GuzzleHttp\Exception\ConnectException;

class APICaller {

    public function callApi($count = 100, $startId = 20){
        $client = new GuzzleHttp\Client();
        try {
            $res = $client->get('http://wonderwall.ox.nfq.lt/kickertable/api/v1/events?rows=' . $count . '&from-id=' . $startId, ['auth' => ['nfq', 'labas']]);
        } catch(ConnectException $e){
            return false;
        }
        return $res->json();
    }

}
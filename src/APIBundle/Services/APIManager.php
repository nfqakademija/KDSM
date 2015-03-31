<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 3/21/2015
 * Time: 2:03 PM
 */

namespace APIBundle\Services;
use Symfony\Component\Form\Exception\BadMethodCallException;

class APIManager {

    protected $caller;
    protected $writer;

//    public function __construct(
//
//        $this->caller = new APICaller('','',''); //added for IDE to be able to recognice the methods....
//    }

    public function setCaller(APICaller $caller){
        $this->caller = $caller;
    }

    public function setParser($type = 'csv', $name = 'failas', $path = 'app/uploads/'){
//        if (!file_exists($path)) {
//            mkdir($path, 0777, true);
//        }
        if ($type == 'xml')
            $this->writer = new APIXmlWriter($path, $name . '.xml');
        else if ($type == 'csv')
            $this->writer = new APICsvWriter($path, $name . '.csv');
        else throw new BadMethodCallException('Incorrect file type provided. Only [csv] and [xml] are accepted');
    }

    public function crawlAPI()
    {
        $writer= $this->writer;
        $caller = $this->caller;

        echo "writing document head... \r\n";
        $writer->writeDocumentHead();

        echo "connecting to api... \r\n";
        $convertedArray = $this->caller->callApi();

        $writer->writeArray($convertedArray);
        echo 'writing api response id= ' . end($convertedArray['records'])['id'] . "\r\n";

        while (sizeof($convertedArray['records']) == 100) {
//        for($i = 0; $i < 10; $i++){

            $convertedArray = $caller->callApi(100, end($convertedArray['records'])['id']);
            $writer->writeArray($convertedArray);
            echo 'writing api response id= ' . end($convertedArray['records'])['id'] . "\r\n";
        }
        echo "writing document footer... \r\n";
        $writer->writeDocumentFooter();
    }
}
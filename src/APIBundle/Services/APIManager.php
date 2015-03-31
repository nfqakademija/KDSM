<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 3/21/2015
 * Time: 2:03 PM
 */

namespace APIBundle\Services;
use Symfony\Component\Form\Exception\BadMethodCallException;
use Exception;

class APIManager {

    protected $caller;
    protected $writer;

    protected $timeStampFrom;

//    public function __construct(
//
//        $this->caller = new APICaller('','',''); //added for IDE to be able to recognice the methods....
//    }

    public function setCaller(APICaller $caller){
        $this->caller = $caller;
    }

    public function setParser($type = 'csv', $name = 'failas', $path = 'app/uploads/', $dateFrom = 0){

        if ($this->isValidTimeStamp($dateFrom))
            $this->timeStampFrom = $dateFrom;
        else if(strtotime($dateFrom))
            $this->timeStampFrom = strtotime($dateFrom . ' 00:00:00');
        else if($dateFrom != 0)
            throw new Exception('Unknown date format: '. $dateFrom);

        if ($type == 'xml')
            $this->writer = new APIXmlWriter($path, $name . '.xml');
        else if ($type == 'csv')
            $this->writer = new APICsvWriter($path, $name . '.csv');
        else throw new BadMethodCallException('Incorrect file type provided. Only [csv] and [xml] are accepted');
    }

    public function crawlAPI($isTimestamp = false)
    {
        $writer= $this->writer;
        $caller = $this->caller;

        echo "writing document head... \r\n";
        $writer->writeDocumentHead();

        echo "connecting to api... \r\n";

        $isTimestamp ? $convertedArray = $this->caller->callApiFromTimeStamp(100, $this->timeStampFrom) :
            $convertedArray = $this->caller->callApiFromId();

        $writer->writeArray($convertedArray);
        echo 'writing api response id= ' . end($convertedArray['records'])['id'] . "\r\n";

        while (sizeof($convertedArray['records']) == 100) {
            $convertedArray = $caller->callApiFromId(100, end($convertedArray['records'])['id']);
            $writer->writeArray($convertedArray);
            echo 'writing api response id= ' . end($convertedArray['records'])['id'] . "\r\n";
        }
        echo "writing document footer... \r\n";
        $writer->writeDocumentFooter();
    }

    private function isValidTimeStamp($timestamp)
    {
        return ((string) (int) $timestamp === $timestamp)
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX);
    }
}
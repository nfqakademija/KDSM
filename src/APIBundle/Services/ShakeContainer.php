<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 3/26/2015
 * Time: 1:34 PM
 */
namespace APIBundle\Services;

class ShakeContainer {
    private $shakes;
    private $iterator;

    public function __construct($path, $fileName){
//        $path = 'app/uploads/';
        $this->iterator = new APICsvIterator($path, $fileName.'.csv', ',', ['id', 'timesec', 'usec', 'type', 'data']);
    }

    public function setShakes(){
        $object = $this->iterator->current();

        $shakeKey = date('Y-m-d H:i:s', $object['timesec']);

        $refDate = date('Y-m-d', $object['timesec']);
        $refDate = date('Y-m-d', strtotime('2014-12-04'));
        $binValue = 0;

        while($refDate != date('Y-m-d', $object['timesec'])) //go until target date
            $object = $this->iterator->current();

        $refTimeStamp = date('H', $object['timesec']) . date('i', $object['timesec']); //get first timestamp

        while($refDate == date('Y-m-d', $object['timesec'])){   //while target date

            if (date('H', $object['timesec']) . date('i', $object['timesec']) == $refTimeStamp){
                if ($object['type'] == 'TableShake')
                    $binValue++;

                $object = $this->iterator->current(); //get next object
            }
            else {
                if($refDate == date('Y-m-d', $object['timesec'])) {
                    $this->shakes[date('Y-m-d H:i:s',strtotime($refDate.substr($refTimeStamp, 0, 2).':'.substr($refTimeStamp, -2).':00'))] = $binValue; //white timebin and event count
                    $binValue = 0; //reset event count

                    $object = $this->iterator->current(); //get next object

                    $refTimeStamp = $this->getNextTimestamp($refTimeStamp);

                    while($refTimeStamp != date('H', $object['timesec']) . date('i', $object['timesec']))
                    {
                        $this->shakes[date('Y-m-d H:i:s',strtotime($refDate.substr($refTimeStamp, 0, 2).':'.substr($refTimeStamp, -2).':00'))] = 0;
                        $refTimeStamp = $this->getNextTimestamp($refTimeStamp);
//                        echo $refTimeStamp . ' ' . date('H', $object['timesec']) . date('i', $object['timesec']);
//                        exit;
                    }
                }
            }
//                $this->shakes[] = date('Y-m-d H:i:s', $object['timesec']);
        }

    }

    public function getShakes(){
        return $this->shakes;
    }

    private function getNextTimestamp($currentTimestamp){
        if($currentTimestamp == 2400)
            return false;
        if(substr($currentTimestamp, -2) < 60) {
//            echo $currentTimestamp;
            $newMin = substr($currentTimestamp, -2);
            $newMin++;

            return ($newMin < 10) ? substr($currentTimestamp, 0, 2) . '0' . $newMin :
                substr($currentTimestamp, 0, 2) . $newMin;
        }
        if(substr($currentTimestamp, -2) == 60)
        {
            $hr = substr($currentTimestamp, 0, 2);
            $hr++;
            return $hr < 10 ? '0' . $hr . '00' : $hr . '00';

        }
    }
}

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
    private $goals;
    private $refDate;
    private $iterator;

    public function __construct($path, $fileName){
        $this->iterator = new APICsvIterator($path, $fileName.'.csv', ',', ['id', 'timesec', 'usec', 'type', 'data']);
        $this->refDate = date('Y-m-d', strtotime('2014-10-03'));
    }

    public function setShakes(){
        $object = $this->iterator->current();

        $shakeValue = $goalValue = 0;

        while($this->refDate != date('Y-m-d', $object['timesec'])) //go until target date
            $object = $this->iterator->current();

        $refTimeStamp = $this->getObjectHrMin($object); //get first timestamp

        while($this->refDate == date('Y-m-d', $object['timesec'])){   //while target date

            if ($this->getObjectHrMin($object) == $refTimeStamp){
                if ($object['type'] == 'TableShake')
                    $shakeValue++;
                if ($object['type'] == 'AutoGoal')
                    $goalValue++;

                $object = $this->getNextObject();
            }
            else {
                if($this->refDate == date('Y-m-d', $object['timesec'])) {
                    $this->writeBinValues($shakeValue, $goalValue, $refTimeStamp);
                    $shakeValue = $goalValue = 0;

                    $object = $this->getNextObject();

                    $refTimeStamp = $this->getNextTimestamp($refTimeStamp);
                    while($refTimeStamp != $this->getObjectHrMin($object))
                    {
                        $this->writeBinValues(0, 0, $refTimeStamp);
                        $refTimeStamp = $this->getNextTimestamp($refTimeStamp);
                    }
                }
            }
        }

    }

    public function getShakes(){
        return $this->shakes;
    }

    public function getGoals(){
        return $this->goals;
    }

    private function getNextObject(){
        return $this->iterator->current();
    }

    private function getObjectHrMin($object){
        return date('H', $object['timesec']) . date('i', $object['timesec']);
    }

    private function writeBinValues($shakes, $goals, $refTimeStamp){
        $this->shakes[date('Y-m-d H:i:s',strtotime($this->refDate.substr($refTimeStamp, 0, 2).':'.substr($refTimeStamp, -2).':00'))] = $shakes; //white timebin and event count
        $this->goals[date('Y-m-d H:i:s',strtotime($this->refDate.substr($refTimeStamp, 0, 2).':'.substr($refTimeStamp, -2).':00'))] = $goals; //white timebin and event count

    }

    private function getNextTimestamp($currentTimeStamp){
        if($currentTimeStamp == 2400)
            return false;
        if(substr($currentTimeStamp, -2) < 60) {
//            echo $currentTimestamp;
            $newMin = substr($currentTimeStamp, -2);
            $newMin++;

            return ($newMin < 10) ? substr($currentTimeStamp, 0, 2) . '0' . $newMin :
                substr($currentTimeStamp, 0, 2) . $newMin;
        }
        if(substr($currentTimeStamp, -2) == 60)
        {
            $hr = substr($currentTimeStamp, 0, 2);
            $hr++;
            return $hr < 10 ? '0' . $hr . '00' : $hr . '00';

        }
    }
}

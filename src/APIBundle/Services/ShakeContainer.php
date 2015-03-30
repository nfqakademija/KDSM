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
//        while ($this->iterator->next()){
        $object = $this->iterator->current();
//        $refHours = date('H', $object['timesec']);
//        $refMins = date('m', $object['timesec']);
        $shakeKey = date('Y-m-d H:i:s', $object['timesec']);
        $refTimeStamp = date('H', $object['timesec']) . date('i', $object['timesec']);
        $refDate = date('Y-m-d', $object['timesec']);
        $refDate = date('Y-m-d', strtotime('2014-12-02'));
        $binValue = 0;
          while($refDate != date('Y-m-d', $object['timesec']))
            $object = $this->iterator->current();
       // for($i = 0; $i<400; $i++) {

        while($refDate == date('Y-m-d', $object['timesec'])){

            $object = $this->iterator->current();
            if (date('H', $object['timesec']) . date('i', $object['timesec']) == $refTimeStamp){
                if ($object['type'] == 'TableShake')
                    $binValue++;
            }
            else {
                if($refDate == date('Y-m-d', $object['timesec'])) {
                    $shakeKey = $refTimeStamp;//date('Y-m-d H:i:s', $object['timesec']);
                    $this->shakes[$shakeKey] = $binValue;
                    $binValue = 0;
                    $refTimeStamp = date('H', $object['timesec']) . date('i', $object['timesec']);
                }
            }
//                $this->shakes[] = date('Y-m-d H:i:s', $object['timesec']);
        }
    }

    public function getShakes(){
        return $this->shakes;
    }
}

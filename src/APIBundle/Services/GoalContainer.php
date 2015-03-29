<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 3/26/2015
 * Time: 1:34 PM
 */
namespace APIBundle\Services;

class GoalContainer {
    private $goals;
    private $iterator;

    public function __construct($path, $fileName){
//        $path = 'app/uploads/';
        $this->iterator = new APICsvIterator($path, $fileName.'.csv', ',', ['id', 'timesec', 'usec', 'type', 'data']);
    }

    public function setGoals(){
//        while ($this->iterator->next()){
        $object = $this->iterator->current();
//        $refHours = date('H', $object['timesec']);
//        $refMins = date('m', $object['timesec']);
        $goalKey = date('Y-m-d H:i:s', $object['timesec']);
        $refTimeStamp = date('H', $object['timesec']) . date('i', $object['timesec']);
        $binValue = 0;
        for($i = 0; $i<400; $i++) {
            $object = $this->iterator->current();
            if (date('H', $object['timesec']) . date('i', $object['timesec']) == $refTimeStamp){
                if ($object['type'] == 'AutoGoal')
                    $binValue++;
            }
            else {
                $this->goals[$goalKey] = $binValue;
                $binValue = 0;
                $goalKey = date('Y-m-d H:i:s', $object['timesec']);
                $refTimeStamp = date('H', $object['timesec']) . date('i', $object['timesec']);
            }
        }
    }

    public function getGoals(){
        return $this->goals;
    }
}

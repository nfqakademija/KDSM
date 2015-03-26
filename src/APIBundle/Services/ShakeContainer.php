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
        for($i = 0; $i<2000; $i++) {
            $object = $this->iterator->current();
            if ($object['type'] == 'TableShake')
                $this->shakes[] = date('Y-m-d H:m:s', $object['timesec']);
        }
    }

    public function getShakes(){
        return $this->shakes;
    }
}
use Symfony\Component\Validator\Tests\Fixtures\Entity;
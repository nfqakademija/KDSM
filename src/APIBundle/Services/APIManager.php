<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 3/21/2015
 * Time: 2:03 PM
 */

namespace APIBundle\Services;

class APIManager {

    protected $caller;
    protected $parser;

    public function setCaller(APICaller $caller){
        $this->caller = $caller;
    }

    public function setParser(APIParser $parser){
        $this->parser = $parser;
    }

    public function getCaller(){
        return $this->caller;
    }

    public function getParser(){
        return $this->parser;
    }
}
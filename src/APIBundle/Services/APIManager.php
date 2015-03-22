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
    protected $writer;

    public function setCaller(APICaller $caller){
        $this->caller = $caller;
    }

    public function setParser(APIXmlWriter $writer){
        $this->writer = $writer;
    }

    public function getCaller(){
        return $this->caller;
    }

    public function getParser(){
        return $this->writer;
    }
}
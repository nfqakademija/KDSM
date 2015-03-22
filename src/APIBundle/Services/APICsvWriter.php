<?php
namespace APIBundle\Services;

use \Symfony\Component\DependencyInjection\ContainerAware;
use APIBundle\Services\APIWriterInterface;

class APICsvWriter extends ContainerAware implements APIWriterInterface{
    protected $fileHandle;

    public function __construct($rootDir, $filePath){
        $this->fileHandle = fopen($rootDir . $filePath, 'w');
    }

    public function writeArray($convertedArray){
        if (is_array($convertedArray['records'])){
            foreach ($convertedArray['records'] as $record)
                fputcsv($this->fileHandle, $record);
        }
        else return false;
    }

    public function writeDocumentHead(){

    }

    public function writeDocumentFooter(){
        fclose($this->fileHandle);
    }

}
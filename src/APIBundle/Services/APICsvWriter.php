<?php
namespace APIBundle\Services;

use \Symfony\Component\DependencyInjection\ContainerAware;

class APICsvWriter extends ContainerAware{
    protected $fileHandle;

    public function __construct($rootDir){
        $this->fileHandle = fopen($rootDir . '/uploads/csv_writer_output.xml', 'w');
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
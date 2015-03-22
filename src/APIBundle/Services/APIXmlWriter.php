<?php
namespace APIBundle\Services;

use \Symfony\Component\DependencyInjection\ContainerAware;
use XMLWriter;
use APIBundle\Services\APIWriterInterface;

class APIXmlWriter extends ContainerAware implements APIWriterInterface{
    protected $xmmWriter;

    public function __construct($rootDir, $filePath){
        $this->xmlWriter = new XMLWriter();
        $this->xmlWriter->openURI($rootDir . $filePath);
    }

    public function writeArray($convertedArray){
        if (is_array($convertedArray['records'])) {
            foreach ($convertedArray['records'] as $record) {
                $this->xmlWriter->startElement('entry');
                $this->xmlWriter->writeElement('id', $record['id']);
                $this->xmlWriter->writeElement('timeSec', date('Y-m-d, H:m:s', $record['timeSec']));
                $this->xmlWriter->writeElement('usec', $record['usec']);
                $this->xmlWriter->writeElement('type', $record['type']);
                $this->xmlWriter->writeElement('data', $record['data']);
                $this->xmlWriter->endElement();
            }
        }
        else return false;
    }

    public function writeDocumentHead(){
        $this->xmlWriter->startDocument('1.0','UTF-8');
        $this->xmlWriter->setIndent(4);
        $this->xmlWriter->startElement('results');
    }

    public function writeDocumentFooter(){
        $this->xmlWriter->endElement();
        $this->xmlWriter->endDocument();
        $this->xmlWriter->flush();
    }

}
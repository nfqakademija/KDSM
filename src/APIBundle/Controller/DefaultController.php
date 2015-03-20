<?php

namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use XMLWriter;
use GuzzleHttp;
use GuzzleHttp\Exception\ConnectException;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $client = new GuzzleHttp\Client();
        try {
            $res = $client->get('http://wonderwall.ox.nfq.lt/kickertable/api/v1/events?rows=100&from-id=20', ['auth' => ['nfq', 'labas']]);
        } catch(ConnectException $e){
            return $this->render('APIBundle:Default:index.html.twig', array('route' => 'Connection error'));
        }
//        $handle = fopen($filename, "r");
//        $contents = fread($handle, filesize($filename));

        $convertedArray = $res->json();

        $xmlWriter = new XMLWriter();
        $xmlWriter->openURI($this->get('kernel')->getRootDir().'/uploads/xml_writer_output.xml');
        $xmlWriter->startDocument('1.0','UTF-8');
        $xmlWriter->setIndent(4);
        $xmlWriter->startElement('results');

        if (is_array($convertedArray['records'])) {
            foreach ($convertedArray['records'] as $record) {
                $xmlWriter->startElement('entry');
                $xmlWriter->writeElement('id', $record['id']);
                $xmlWriter->writeElement('timeSec', date('Y-m-d, H:m:s', $record['timeSec']));
                $xmlWriter->writeElement('usec', $record['usec']);
                $xmlWriter->writeElement('type', $record['type']);
                $xmlWriter->writeElement('data', $record['data']);
                $xmlWriter->endElement();
            }
        }
        else return $this->render('APIBundle:Default:index.html.twig', array('route' => 'Illegal API result'));

        while(sizeof($convertedArray['records']) == 100) {
//        for($i = 0; $i < 10; $i++){
            try {
                $res = $client->get('http://wonderwall.ox.nfq.lt/kickertable/api/v1/events?rows=100&from-id=' . end($convertedArray['records'])['id'], ['auth' => ['nfq', 'labas']]);
            } catch(ConnectException $e){
                return $this->render('APIBundle:Default:index.html.twig', array('route' => 'Connection error'));
            }

            $convertedArray = $res->json();

            if (is_array($convertedArray['records'])) {
                foreach ($convertedArray['records'] as $record) {
                    $xmlWriter->startElement('entry');
                    $xmlWriter->writeElement('id', $record['id']);
                    $xmlWriter->writeElement('timeSec', date('Y-m-d, H:m:s', $record['timeSec']));
                    $xmlWriter->writeElement('usec', $record['usec']);
                    $xmlWriter->writeElement('type', $record['type']);
                    $xmlWriter->writeElement('data', $record['data']);
                    $xmlWriter->endElement();
                }
            }
            else return $this->render('APIBundle:Default:index.html.twig', array('route' => 'Illegal API result'));
        }
            $xmlWriter->endElement();
            $xmlWriter->endDocument();

            $xmlWriter->flush();
            return $this->render('APIBundle:Default:index.html.twig', array('route' => end($convertedArray['records'])['id']));
    }
}

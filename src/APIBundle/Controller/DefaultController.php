<?php

namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use XMLWriter;
use GuzzleHttp;

class DefaultController extends Controller
{
    public function indexAction()
    {
//        $filename = $this->get('kernel')->getRootDir().'/uploads/json_data.json';
//        $filename = "json_data.json";
//        $handle = fopen($filename, "r");
//        $contents = fread($handle, filesize($filename));
//        $converted_array = json_decode($contents);
        $client = new GuzzleHttp\Client();
        $res = $client->get('http://wonderwall.ox.nfq.lt/kickertable/api/v1/events?rows=100&from-id=20', ['auth' =>  ['nfq', 'labas']]);

        $convertedArray = $res->json();
//        print_r($convertedArray['records']);
//        exit;
        $xmlWriter = new XMLWriter();
        $xmlWriter->openURI($this->get('kernel')->getRootDir().'/uploads/xml_writer_output.xml');
        $xmlWriter->startDocument('1.0','UTF-8');
        $xmlWriter->setIndent(4);
        $xmlWriter->startElement('results');
        foreach($convertedArray['records'] as $record)
        {
            $xmlWriter->startElement('entry');
            $xmlWriter->writeElement('id', $record['id']);
            $xmlWriter->writeElement('timeSec', date('Y-m-d, H:m:s',$record['timeSec']));
            $xmlWriter->writeElement('usec', $record['usec']);
            $xmlWriter->writeElement('type', $record['type']);
            $xmlWriter->writeElement('data', $record['data']);
            $xmlWriter->endElement();
        }
        $xmlWriter->endElement();
        $xmlWriter->endDocument();

        $xmlWriter->flush();

        return $this->render('APIBundle:Default:index.html.twig', array('route' => $this->get('kernel')->getRootDir()));
    }
}

<?php
namespace APIBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use XMLWriter;
use GuzzleHttp;
use GuzzleHttp\Exception\ConnectException;

class DumpAPICommand extends ContainerAwareCommand{
    protected function configure()
    {
        $this->setName('api:dump');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('API Dump Running');
        $client = new GuzzleHttp\Client();
        try {
            $res = $client->get('http://wonderwall.ox.nfq.lt/kickertable/api/v1/events?rows=100&from-id=20', ['auth' => ['nfq', 'labas']]);
        } catch(ConnectException $e){
            $output->writeln('Connection error');
        }

        $convertedArray = $res->json();

        $xmlWriter = new XMLWriter();
        $this->getContainer()->get('kernel')->getRootDir();
        $xmlWriter->openURI($this->getContainer()->get('kernel')->getRootDir() . '/uploads/xml_writer_output.xml');
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
        else $output->writeln('Illegal API result');

        while(sizeof($convertedArray['records']) == 100) {
//        for($i = 0; $i < 10; $i++){
            try {
                $res = $client->get('http://wonderwall.ox.nfq.lt/kickertable/api/v1/events?rows=100&from-id=' . end($convertedArray['records'])['id'], ['auth' => ['nfq', 'labas']]);
            } catch(ConnectException $e){
                $output->writeln('Connection error');
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
                    $xmlWriter->flush();
                }
            }
            else $output->writeln('Illegal API result');
        }
        $xmlWriter->endElement();
        $xmlWriter->endDocument();

        $xmlWriter->flush();
        $output->writeln('API dump complete, last index of table event:');
        $output->writeln(end($convertedArray['records'])['id']);
    }

}

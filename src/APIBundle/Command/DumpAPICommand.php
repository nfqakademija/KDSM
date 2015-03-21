<?php
namespace APIBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//use APIBundle\Services\

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

        $manager = $this->getContainer()->get('api.api_manager');

        $caller = $this->getContainer()->get('api.api_caller');
        $parser = $this->getContainer()->get('api.api_parser');

//        $caller = $manager->getCaller();
//        $parser = $manager->getParser();

        $parser->writeDocumentHead();

        $convertedArray = $caller->callApi();
        $parser->writeArray($convertedArray);

        while(sizeof($convertedArray['records']) == 100) {
//        for($i = 0; $i < 10; $i++){
            $convertedArray = $caller->callApi(100, end($convertedArray['records'])['id']);
            $parser->writeArray($convertedArray);
            $output->writeln('Dumping record id: ' . end($convertedArray['records'])['id']);
        }

        $parser->writeDocumentFooter();

        $output->writeln('API dump complete, last index of table event:');
        $output->writeln(end($convertedArray['records'])['id']);
    }

}

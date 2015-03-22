<?php
namespace APIBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use GuzzleHttp;

class DumpAPICommand extends ContainerAwareCommand{
    protected function configure()
    {
        $this->setName('api:dump')
        ->addArgument('dumpType', InputArgument::REQUIRED, 'Dump Type');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dumpType = $input->getArgument('dumpType');
        if($dumpType == 'xml')
            $writer = $this->getContainer()->get('api.api_xml_writer');
        else if ($dumpType == 'csv')
            $writer = $this->getContainer()->get('api.api_csv_writer');
        else{
            $output->writeln('Unrecogniced dump file format');
            return false;
        }
        $output->writeln('API Dump Running');

//        $manager = $this->getContainer()->get('api.api_manager');

        $caller = $this->getContainer()->get('api.api_caller');
//        $writer = $this->getContainer()->get('api.api_xml_writer');

//        $caller = $manager->getCaller();
//        $parser = $manager->getParser();

        $writer->writeDocumentHead();

        $convertedArray = $caller->callApi();
        $writer->writeArray($convertedArray);

        while(sizeof($convertedArray['records']) == 100) {
//        for($i = 0; $i < 10; $i++){
            $convertedArray = $caller->callApi(100, end($convertedArray['records'])['id']);
            $writer->writeArray($convertedArray);
            $output->writeln('Dumping record id: ' . end($convertedArray['records'])['id']);
        }

        $writer->writeDocumentFooter();

        $output->writeln('API dump complete, last index of table event:');
        $output->writeln(end($convertedArray['records'])['id']);
    }

}

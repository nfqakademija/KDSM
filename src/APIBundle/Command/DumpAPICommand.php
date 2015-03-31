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
        ->addOption('t',null, InputOption::VALUE_NONE)
        ->addArgument('dumpType', InputArgument::OPTIONAL, 'Dump Type')
        ->addArgument('fileName', InputArgument::OPTIONAL, 'File Name')
        ->addArgument('dayToDump', InputArgument::OPTIONAL, 'Day or unix timestamp from which the dump should be made');
//        ->addArgument('filePath', InputArgument::OPTIONAL, 'File Path');
        //failo kelias
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiManager = $this->getContainer()->get('api.api_manager');

        //ifus ismest i manageri
        if($input->getOption('t')) {
            $dumpType = $input->getArgument('dumpType');
            $fileName = $input->getArgument('fileName');
            $dayToDump = $input->getArgument('dayToDump');
            echo strtotime($dayToDump);
            $filePath = 'app/uploads/'; //leaving hardcoded for now, as the file save location should not vary.
//        $filePath = $input->getArgument('filePath');
            $apiManager->setParser($dumpType, $fileName, $filePath, $dayToDump);
        }
        else
            $apiManager->setParser();

        if($input->getOption('t') && $dayToDump != 0)
            $apiManager->crawlAPI(true);
        else
            $apiManager->crawlAPI();

        $output->writeln('API dump complete, last index of table event:');
    }

}

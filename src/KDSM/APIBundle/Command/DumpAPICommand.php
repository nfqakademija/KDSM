<?php
namespace KDSM\APIBundle\Command;

use GuzzleHttp;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DumpAPICommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('api:dump')
            ->addOption('t', null, InputOption::VALUE_NONE)
            ->addArgument('dumpType', InputArgument::OPTIONAL, 'Dump Type')
            ->addArgument('fileName', InputArgument::OPTIONAL, 'File Name')
            ->addArgument('filePath', InputArgument::OPTIONAL, 'File Path');
        //failo kelias
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiManager = $this->getContainer()->get('api.api_manager');
        if ($input->getOption('t')) {
            $dumpType = $input->getArgument('dumpType');
            $fileName = $input->getArgument('fileName');
            $filePath = 'app/uploads/'; //leaving hardcoded for now, as the file save location should not vary.
//        $filePath = $input->getArgument('filePath');
            $apiManager->setParser($dumpType, $fileName, $filePath);
        } else {
            $apiManager->setParser();
        }

        $apiManager->crawlAPI();

        $output->writeln('API dump complete, last index of table event:');
    }

}

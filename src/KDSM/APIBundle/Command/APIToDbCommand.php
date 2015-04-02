<?php
namespace KDSM\APIBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use GuzzleHttp;

class APIToDbCommand extends ContainerAwareCommand{
    protected function configure()
    {
        $this->setName('api:getlatest')
        ->addOption('all',null, InputOption::VALUE_NONE);
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dbManager = $this->getContainer()->get('api.api_db_manager');
        $input->getOption('all') ?
            $dbManager->getLatest($this->getContainer()->get('api.api_caller'), true) :
            $dbManager->getLatest($this->getContainer()->get('api.api_caller'), false);
        $output->writeln('API dump complete, last index of table event:');
    }

}

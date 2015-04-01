<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 3/23/2015
 * Time: 9:50 AM
 */

namespace APIBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


use Doctrine\ORM\EntityManager;
use APIBundle\Entity\TableEventTypeRepository;

class IterateCsvCommand extends ContainerAwareCommand{
    protected function configure()
    {
        $this->setName('api:iterate');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $iterator = $this->getContainer()->get('api.api_csv_iterator');
        $dbManager = $this->getContainer()->get('api.api_db_manager');
        $dbManager->writeCsvToDb($iterator);
    }

}
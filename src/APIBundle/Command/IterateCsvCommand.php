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

class IterateCsvCommand extends ContainerAwareCommand{
    protected function configure()
    {
        $this->setName('api:iterate')
            ->addArgument('count', InputArgument::OPTIONAL, 'Desired element count');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $iterator = $this->getContainer()->get('api.api_csv_iterator');
        if($input->getArgument('count') && is_numeric($input->getArgument('count'))){
            for($i = 0; $i < $input->getArgument('count'); $i++){
                print_r($iterator->current());
            }
        }
        else
            while ($iterator->next())
                print_r($iterator->current());
    }

}
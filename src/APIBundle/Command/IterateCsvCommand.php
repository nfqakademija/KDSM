<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 3/23/2015
 * Time: 9:50 AM
 */

namespace APIBundle\Command;

use APIBundle\Services\APICsvIterator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IterateCsvCommand extends ContainerAwareCommand{
    protected function configure()
    {
        $this->setName('api:iterate')
            ->addArgument('fileName', InputArgument::REQUIRED, 'csv file location')
            ->addArgument('count', InputArgument::OPTIONAL, 'Desired element count');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = 'app/uploads/';
        $file = $input->getArgument('fileName');
        $iterator = new APICsvIterator($path, $file . '.csv', ',', ['id', 'timesec', 'usec', 'type', 'data']);
//        $iterator = $this->getContainer()->get('api.api_csv_iterator');
        if($input->getArgument('count') && is_numeric($input->getArgument('count'))){
            for($i = 0; $i < $input->getArgument('count'); $i++){
//                print_r($iterator->current());
                echo date('Y-m-d', $iterator->current()['timesec']);
            }
        }
        else {
//            $i = 0;
//            foreach ($iterator as $key => $row) {
//                $i += $i < count($row) - 1 ? 1 : -$i;
//                echo "{$key}: {$i}: {$row[$i]}\n";
//            }
//        }
            while ($iterator->next())
                //print_r($iterator->current());
                echo date('Y-m-d', $iterator->current()['timesec'])."\n";
        }
    }

}
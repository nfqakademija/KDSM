<?php
/**
 * Created by PhpStorm.
 * User: Martynas
 * Date: 5/11/2015
 * Time: 5:43 PM
 */

namespace KDSM\ContentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateStatisticsCommand extends ContainerAwareCommand{

    protected function configure(){
        $this->setName('stats:update');
//        ->addOption('');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $updater = $this->getContainer()->get('kdsm_content.statistics_updater');
        $updater->update();
        $output->writeln('Statistics successfully updated.');

    }

}
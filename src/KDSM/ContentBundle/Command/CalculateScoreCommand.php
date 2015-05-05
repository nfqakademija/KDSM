<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 3/23/2015
 * Time: 9:50 AM
 */

namespace KDSM\ContentBundle\Command;

use KDSM\APIBundle\Entity\TableEventTypeRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class CalculateScoreCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('cont:calculate');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $calculator = $this->getContainer()->get('kdsm_content.live_score_manager');
        $status = $calculator->getTableStatus();
        $output->writeln('Table status calculated at: ' . date('Y-m-d H:i:s', strtotime('now')) . '. Table status: ' . $status);
    }

}
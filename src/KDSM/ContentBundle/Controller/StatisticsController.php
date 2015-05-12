<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends Controller
{
    public function indexAction()
    {
        $statisticsService = $this->get('kdsm_content.statistics_updater');
        $stats = $statisticsService->getStatistics();

        return $this->render('KDSMContentBundle:Statistics:index.html.twig', array(
            'stats' => json_encode($stats)
        ));
    }
}

<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatisticsController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMContentBundle:Statistics:index.html.twig');
    }
}

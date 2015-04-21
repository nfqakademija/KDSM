<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMContentBundle:Statistics:index.html.twig');
    }
}

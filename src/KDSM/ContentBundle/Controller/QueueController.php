<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class QueueController extends Controller
{

    public function indexAction()
    {
        return $this->render('KDSMContentBundle:Queue:index.html.twig', array('route' => $this->get('kernel')->getRootDir()));

    }

}

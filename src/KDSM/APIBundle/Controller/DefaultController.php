<?php

namespace KDSM\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMAPIBundle:Default:index.html.twig', array('route' => $this->get('kernel')->getRootDir()));
    }
}

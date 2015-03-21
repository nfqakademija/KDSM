<?php

namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('APIBundle:Default:index.html.twig', array('route' => $this->get('kernel')->getRootDir()));
    }
}

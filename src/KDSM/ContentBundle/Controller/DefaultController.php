<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMContentBundle:Default:index.html.twig');
    }

    public function loggedHomepageAction()
    {
        return $this->render('KDSMContentBundle:Default:loggedHomepage.html.twig');
    }
}

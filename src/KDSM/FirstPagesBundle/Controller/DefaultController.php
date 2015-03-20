<?php

namespace KDSM\FirstPagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMFirstPagesBundle:Default:index.html.twig');
    }
}

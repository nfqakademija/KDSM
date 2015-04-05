<?php

namespace KDSM\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UsersController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMAPIBundle:Users:index.html.twig', array('route' => $this->get('kernel')->getRootDir()));
    }

    public function viewAction()
    {
        return $this->render('KDSMAPIBundle:Users:view.html.twig', array('route' => $this->get('kernel')->getRootDir()));

    }
}

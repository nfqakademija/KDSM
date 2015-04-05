<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UsersController extends Controller
{

    public function loginAction()
    {
        return $this->render('KDSMContentBundle:Users:login.html.twig', array('route' => $this->get('kernel')->getRootDir()));

    }

    public function registerAction()
    {
        return $this->render('KDSMContentBundle:Users:register.html.twig', array('route' => $this->get('kernel')->getRootDir()));

    }

    public function indexAction()
    {
        return $this->render('KDSMContentBundle:Users:index.html.twig', array('route' => $this->get('kernel')->getRootDir()));
    }

    public function viewAction()
    {
        return $this->render('KDSMContentBundle:Users:view.html.twig', array('route' => $this->get('kernel')->getRootDir()));

    }

}

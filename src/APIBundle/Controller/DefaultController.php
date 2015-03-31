<?php

namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use APIBundle\Services\ShakeContainer;

class DefaultController extends Controller
{
    public function indexAction()
    {
        
        $shakeContainer = new ShakeContainer($this->get('kernel')->getRootDir()."/uploads/", 'failas');
        $shakeContainer->setShakes();
        return $this->render('APIBundle:Default:index.html.twig', array('shakes' => $shakeContainer->getShakes(),
            'goals' => $shakeContainer->getGoals()));
    }
}

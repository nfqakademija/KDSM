<?php

namespace APIBundle\Controller;

use APIBundle\Services\GoalContainer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use APIBundle\Services\ShakeContainer;

class DefaultController extends Controller
{
    public function indexAction()
    {
        
        $shakeContainer = new ShakeContainer($this->get('kernel')->getRootDir()."/uploads/", 'failas');
        $shakeContainer->setShakes();
        $goalContainer = new GoalContainer($this->get('kernel')->getRootDir()."/uploads/", 'failas');
        $goalContainer->setGoals();
        return $this->render('APIBundle:Default:index.html.twig', array('shakes' => $shakeContainer->getShakes(),
            'goals' => $goalContainer->getGoals()));
    }
}

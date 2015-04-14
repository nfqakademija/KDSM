<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use KDSM\ContentBundle\Form\ParameterType;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMContentBundle:Admin:index.html.twig', array(
                // ...
            ));    }

}

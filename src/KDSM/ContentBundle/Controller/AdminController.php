<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMContentBundle:Admin:index.html.twig', array(
                // ...
            ));    }

}

<?php

namespace KDSM\FirstPagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMFirstPagesBundle:Home:index.html.twig', array(
                // ...
            ));    }

}

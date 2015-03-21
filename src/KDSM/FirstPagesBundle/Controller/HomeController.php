<?php

namespace KDSM\FirstPagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMFirstPagesBundle:Home:index.html.twig', array(
                'navigation' => array(
                    'reservation' => array(
                        'path' => 'reservationpage',
                        'name' => 'reservation page'
                    ),
                    'profile' => array(
                        'path' => 'profilepage',
                        'name' => 'your profile page'
                    )
                )
            ));    }

}

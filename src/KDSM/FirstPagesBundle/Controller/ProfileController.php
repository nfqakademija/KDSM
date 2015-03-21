<?php

namespace KDSM\FirstPagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfileController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMFirstPagesBundle:Profile:index.html.twig', array(
                'navigation' => array(
                    'reservation' => array(
                        'path' => 'reservationpage',
                        'name' => 'reservation page'
                    ),
                    'home' => array(
                        'path' => 'homepage',
                        'name' => 'home page'
                    )
                )
            ));    }

}

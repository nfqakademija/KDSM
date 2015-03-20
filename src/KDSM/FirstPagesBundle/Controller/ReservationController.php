<?php

namespace KDSM\FirstPagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReservationController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMFirstPagesBundle:Reservation:index.html.twig', array(
                // ...
            ));    }

}

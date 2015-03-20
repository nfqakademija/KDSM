<?php

namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use XMLWriter;
use GuzzleHttp;
use GuzzleHttp\Exception\ConnectException;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('APIBundle:Default:index.html.twig', array('route' => $this->get('kernel')->getRootDir()));
    }
}

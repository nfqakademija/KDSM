<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class QueueController extends Controller
{

    public function indexAction()
    {
        return $this->render('KDSMContentBundle:Queue:index.html.twig',
            array('route' => $this->get('kernel')->getRootDir()));

    }

    public function getLookingForGameUsersAction(){
        $userEm = $this->getDoctrine()->getEntityManager();
        $userRep = $userEm->getRepository('KDSMContentBundle:User');
        $users = $userRep->getUsersLookingForGame();
//        print_r($users);
        foreach ($users as $user)
        {
            $result = $user->getId();
        }

//        $result = json_encode($userRep->getUsersLookingForGame());
        $response = new Response($result);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    public function getQueueListAction()
    {
        $response = new Response(1);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function createPendingQueueElementAction()
    {
        $response = new Response(1);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function sendUserQueueJoinRequestAction($userIds = null, $queueId = null)
    {
        $response = new Response(1);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function userQueueJoinAcceptAction($userId = null, $queueId = null)
    {
        $response = new Response(1);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function userQueueJoinDeclineAction($userId = null, $queueId = null)
    {
        $response = new Response(1);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}

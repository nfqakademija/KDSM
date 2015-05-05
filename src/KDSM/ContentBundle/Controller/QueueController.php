<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class QueueController extends Controller
{

    public function indexAction()
    {
        return $this->render('KDSMContentBundle:Queue:index.html.twig',
            array('route' => $this->get('kernel')->getRootDir()));

    }

    public function queueAction($method, $queueId = null)
    {
        $queueMan = $this->get('kdsm_content.queue_manager');

        switch($method) {
            case 'list':
                $queueListResponse = new JsonResponse($queueMan->getCurrentQueueList());

                return $queueListResponse;
            case 'create':
                if ($queueId != null) {
                    $request = Request::createFromGlobals();
                    $request->request->get('usersIds');
                    $managerResponse = $queueMan->joinQueueRequest($queueId, $_POST['usersIds']);
                 }
                else{
                    $managerResponse = $queueMan->createNewQueueElement($this->get('security.token_storage')->getToken()
                        ->getUser());
                }
                $userResponse = new JsonResponse($managerResponse);
                return $userResponse;

                //return $this->render('KDSMContentBundle:Queue:queue.html.twig', array('queue' => $managerResponse));
            case 'accept_invite':
                $managerResponse = $queueMan->joinQueueRequest($queueId, $this->get('security.token_storage')->getToken()
                    ->getUser());
                return $this->render('KDSMContentBundle:Queue:queue.html.twig', array('queue' => $managerResponse));
            case 'lfg':
                $userEm = $this->getDoctrine()->getEntityManager();
                $userRep = $userEm->getRepository('KDSMContentBundle:User');
                $userResponse = new JsonResponse($userRep->getUsersLookingForGame($this->get('security.token_storage')->getToken()
                    ->getUser()));
                return $userResponse;
            default:
                throw new NotFoundHttpException('Page not found');
                break;
        }
    }

    public function sendUserQueueJoinRequestAction($userIds = null, $queueId = null)
    {
        $response = new Response(1);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function userQueueJoinDeclineAction($queueId = null)
    {
        $response = new Response(1);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}

<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\GenericEvent;

class QueueController extends Controller
{

    public function indexAction()
    {
        return $this->render('KDSMContentBundle:Includes:queue.html.twig',
            array('route' => $this->get('kernel')->getRootDir()));

    }

    public function queueAction($method, $queueId = null)
    {
        $queueMan = $this->get('kdsm_content.queue_manager');

        switch($method) {
            case 'list':
                $response = $queueMan->getCurrentQueueList();
                $queueListResponse = new JsonResponse($response);

                return $queueListResponse;
            case 'create':
                $request = Request::createFromGlobals();
                $request->request->get('usersIds');
                $users = $_POST['usersIds'];
                $queueMan->sendInvites($_POST['usersIds'], 99);

                array_splice($users, 0, 0, (string)$this->get('security.token_storage')->getToken()
                    ->getUser()->getId());
                $managerResponse = $queueMan->queueCreateRequest($users);
//                $queueMan->sendInvites($_POST['usersIds'], $managerResponse['queueId'], $this->get('event_dispatcher'));

//                $dispatcher = $this->get('event_dispatcher');
//                foreach ($_POST['usersIds'] as $userId){
//                    $event = new GenericEvent();
//                    $event->setArgument('gameid', $managerResponse['queueId']);
//                    $event->setArgument('userid', $userId);
//                    $
//                    $dispatcher->dispatch('kdsm_content.notification_create', $event);
//                }

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

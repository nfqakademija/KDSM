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
        return $this->render('KDSMContentBundle:Includes:queue.html.twig',
            array('route' => $this->get('kernel')->getRootDir()));

    }

    public function queueAction($method, $queueId = null)
    {
        $queueMan = $this->get('kdsm_content.queue_manager');

        switch($method) {
            case 'single_queue':
                $response = $queueMan->getSingleQueue($queueId, (string)$this->get('security.token_storage')->getToken()
                    ->getUser()->getId());
                $queueListResponse = new JsonResponse($response);
                return $queueListResponse;
            case 'list':
                $response = $queueMan->getCurrentQueueList((string)$this->get('security.token_storage')->getToken()
                    ->getUser()->getId());
                $queueListResponse = new JsonResponse($response);
                return $queueListResponse;
            case 'create':
                $request = Request::createFromGlobals();
                $request->request->get('usersIds');
                $users = $_POST['usersIds'];
                array_splice($users, 0, 0, (string)$this->get('security.token_storage')->getToken()
                    ->getUser()->getId());
                $managerResponse = $queueMan->queueCreateRequest($users, (string)$this->get('security.token_storage')->getToken()
                    ->getUser()->getId());
                $userResponse = new JsonResponse($managerResponse);
                return $userResponse;
            case 'remove':
                $response = $queueMan->removeQueue($queueId, (string)$this->get('security.token_storage')->getToken()
                    ->getUser()->getId());
                $queueRemoveResponse = new JsonResponse($response);
                return $queueRemoveResponse;
            case 'process_invite':
                $request = Request::createFromGlobals();
                $userId = $_POST['userId'];
                $response = $_POST['userResponse'];
                $managerResponse = $queueMan->processUserInviteResponse($queueId, $userId, $response);
                $userResponse = new JsonResponse($managerResponse);
                return $userResponse;
            case 'lfg':
                $userEm = $this->getDoctrine()->getEntityManager();
                $userRep = $userEm->getRepository('KDSMContentBundle:User');
                $userResponse = new JsonResponse($userRep->getUsersLookingForGame($this->get('security.token_storage')->getToken()
                    ->getUser()));
                return $userResponse;
            case 'join_users':
                $users = $_POST['usersIds'];
                $managerResponse = $queueMan->queueAddUsersRequest($users, $queueId);
                $userResponse = new JsonResponse($managerResponse);
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

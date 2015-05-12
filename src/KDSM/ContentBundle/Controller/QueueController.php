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
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('KDSMContentBundle:Includes:queue.html.twig',
                array('route' => $this->get('kernel')->getRootDir()));
        } else {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
    }

    public function queueAction($method, $queueId = null, Request $request = null)
    {
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
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
                    $users = $request->request->get('usersIds');
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
                    $userId = $request->request->get('userId');
                    $userResponse = $request->request->get('userResponse');
                    $managerResponse = $queueMan->processUserInviteResponse($queueId, $userId, $userResponse);
                    $queueResponse = new JsonResponse($managerResponse);
                    return $queueResponse;
                case 'lfg':
                    $userEm = $this->getDoctrine()->getEntityManager();
                    $userRep = $userEm->getRepository('KDSMContentBundle:User');
                    $userResponse = new JsonResponse($userRep->getUsersLookingForGame($this->get('security.token_storage')->getToken()
                        ->getUser()));
                    return $userResponse;
                case 'join_users':
                    $users = $request->request->get('usersIds');

                    $managerResponse = $queueMan->queueAddUsersRequest($users, $queueId);
                    $userResponse = new JsonResponse($managerResponse);
                    return $userResponse;
                default:
                    throw new NotFoundHttpException('Page not found');
                    break;
            }
        } else {
            throw new \Exception('User is not logged in!');
        }
    }
}

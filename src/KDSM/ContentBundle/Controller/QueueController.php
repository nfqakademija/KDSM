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
            $currentUserId = (string)$this->get('security.token_storage')->getToken()->getUser()->getId();
            $response = null;

            switch($method) {
                case 'single_queue':
                    $response = $queueMan->getSingleQueue($queueId, $currentUserId);
                    break;
                case 'list':
                    $response = $queueMan->getCurrentQueueList($currentUserId);
                    break;
                case 'create':
                    $users = $request->request->get('usersIds');
                    array_splice($users, 0, 0, $currentUserId);
                    $response = $queueMan->queueCreateRequest($users, $currentUserId);
                    break;
                case 'remove':
                    $response = $queueMan->removeQueue($queueId, $currentUserId);
                    break;
                case 'process_invite':
                    $userId = $request->request->get('userId');
                    $userResponse = $request->request->get('userResponse');
                    $response = $queueMan->processUserInviteResponse($queueId, $userId, $userResponse);
                    break;
                case 'lfg':
                    $userEm = $this->getDoctrine()->getEntityManager();
                    $userRep = $userEm->getRepository('KDSMContentBundle:User');
                    $currentUserObject = $this->get('security.token_storage')->getToken()->getUser();
                    $response = $userRep->getUsersLookingForGame($currentUserObject);
                    break;
                case 'join_users':
                    $users = $request->request->get('usersIds');
                    $response = $queueMan->queueAddUsersRequest($users, $queueId);
                    break;
                default:
                    throw new NotFoundHttpException('Page not found');
                    break;
            }
            $queueResponse = new JsonResponse($response);
            return $queueResponse;
        } else {
            throw new \Exception('User is not logged in!');
        }
    }
}

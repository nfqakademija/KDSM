<?php

namespace KDSM\ContentBundle\Controller;

use KDSM\ContentBundle\Entity\Notification;
use KDSM\ContentBundle\EventListener\KDSMNotificationListener;
use KDSM\ContentBundle\KDSMContentBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;


class DefaultController extends Controller
{
    public function indexAction()
    {
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('KDSMContentBundle:Default:tableDataMain.html.twig');
        } else {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
    }

    public function loggedHomepageAction()
    {
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('KDSMContentBundle:Default:tableDataMain.html.twig');
        } else {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
    }

    public function liveGameAction()
    {
        $cacheMan = $this->get('kdsm_content.cache_manager');
        $players = $cacheMan->getPlayerCache();

//        $liveScoreManager = $this->get('kdsm_content.live_score_manager');
//        $liveScoreManager->getTableStatus();

        $tableStatusResponse = [
            'status' => $cacheMan->getTableStatusCache(),
            'scoreWhite' => $cacheMan->getScoreCache()['score']['white'],
            'scoreBlack' => $cacheMan->getScoreCache()['score']['black'],
            'player1' => $players['players']['player1'],
            'player2' => $players['players']['player2'],
            'player3' => $players['players']['player3'],
            'player4' => $players['players']['player4'],

        ];

        $response = new JsonResponse($tableStatusResponse);
        return $response;
    }

    public function getNotificationsAction(Request $request)
    {
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $encoders = array(new JsonEncoder());
            $normalizers = array(new GetSetMethodNormalizer());
            $serializer = new Serializer($normalizers, $encoders);

            $em = $this->getDoctrine()->getEntityManager();
            $rep = $em->getRepository('KDSMContentBundle:Notification');
            $notifications = $rep->getAllUnviewedNotifications($request->request->get('id'));

            $notificationsjson = $serializer->serialize($notifications, 'json');

            return new Response($notificationsjson);
        } else {
            throw new \Exception('User is not logged in!');
        }
    }

    public function viewNotificationAction(Request $request)
    {
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $em = $this->getDoctrine()->getEntityManager();
            $rep = $em->getRepository('KDSMContentBundle:Notification');
            $rep->setViewed($request->request->get('id'));
            return new Response();
        } else {
            throw new \Exception('User is not logged in!');
        }
    }
}

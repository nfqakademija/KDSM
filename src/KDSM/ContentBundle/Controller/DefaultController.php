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
        if ( $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('KDSMContentBundle:Default:tableDataMain.html.twig');
        }
        else {
            return $this->render('KDSMContentBundle:Default:index.html.twig');
        }
    }

    public function loggedHomepageAction()
    {
        return $this->render('KDSMContentBundle:Default:tableDataMain.html.twig');
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

        $rand = rand(1,10);
        $users = array(125234243, 135513113, 643434232, 533435335, 234234236, '', '', '', '', '');
        $result = array();
        if($rand <= 5) {
            $result =  array('status' => 'free');
        }
        if($rand > 3){
            $result = array('status' => 'busy', 'player1' => $users[array_rand($users)], 'player2' => $users[array_rand($users)],
                'player3' => $users[array_rand($users)], 'player4' => $users[array_rand($users)], 'scoreWhite' => rand(0,10), 'scoreBlack' => rand(5,10));
        }

        $result = $tableStatusResponse;

        $response = new JsonResponse($result);
        return $response;
    }

    public function getNotificationsAction(Request $request){
        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $em = $this->getDoctrine()->getEntityManager();
        $rep = $em->getRepository('KDSMContentBundle:Notification');
        $notifications = $rep->getAllUnviewedNotifications($request->request->get('id'));

        $notificationsjson = $serializer->serialize($notifications, 'json');

        return new Response($notificationsjson);
    }

    public function viewNotificationAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $rep = $em->getRepository('KDSMContentBundle:Notification');
        $rep->setViewed($request->request->get('id'));
        return new Response();
    }

    public function testEventAction(){
        $dispatcher = new EventDispatcher();
        $em = $this->getDoctrine()->getEntityManager();

        $listener = new KDSMNotificationListener($em);
        $dispatcher->addListener('kdsm_content.notification_create', array($listener, 'onNotificationCreate'));

        $event = new GenericEvent();
        $event->setArgument('gameid', 123);
        $event->setArgument('userid', 1);

        $dispatcher->dispatch('kdsm_content.notification_create', $event);

        return new Response();
    }
}

<?php

namespace KDSM\ContentBundle\Controller;

use KDSM\ContentBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;


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
//        $cacheMan = $this->get('kdsm_content.cache_manager');
//
//        $liveScoreManager = $this->get('kdsm_content.live_score_manager');
//        $liveScoreManager->getTableStatus();
//
//        $tableStatusResponse = [
//            'tableStatus' => $cacheMan->getTableStatusCache(),
//            'score' => $cacheMan->getScoreCache()['score']
//        ];


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

        $result = json_encode($result);
        $response = new Response($result);
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
}

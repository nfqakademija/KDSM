<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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

        $tableStatusResponse = [
            'tableStatus' => $cacheMan->getTableStatusCache(),
            'score' => $cacheMan->getScoreCache()['score']
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

        $result = json_encode($result);
        $response = new Response($result);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}

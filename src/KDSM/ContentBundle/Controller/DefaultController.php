<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Predis;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('KDSMContentBundle:Default:index.html.twig');
    }

    public function loggedHomepageAction()
    {
        return $this->render('KDSMContentBundle:Default:tableDataMain.html.twig');
    }

    public function liveGameAction(){
        $cacheMan = $this->get('kdsm_content.cache_manager');

        $liveScoreManager = $this->get('kdsm_content.live_score_manager');
        $liveScoreManager->getTableStatus();

        $tableStatusResponse = ['tableStatus' => $cacheMan->getTableStatusCache(), 'score' => $cacheMan->getScoreCache()['score']];



//        $rand = rand(1,10);
//        $users = array(125234243, 135513113, 643434232, 533435335, 234234236, '', '', '', '', '');
//        if($rand <= 5) {
//            $result =  json_encode(array('status' => 'free'));
//        }
//        if($rand > 3){
//            $result = json_encode(array('status' => 'busy', 'player1' => $users[array_rand($users)], 'player2' => $users[array_rand($users)],
//                'player3' => $users[array_rand($users)], 'player4' => $users[array_rand($users)], 'scoreWhite' => rand(0,10), 'scoreBlack' => rand(5,10)));
//        }

        $rand = rand(1,10);
        $users = array(125234243, 135513113, 643434232, 533435335, 234234236);
        $result = array();
//        if($rand <= 5) {
//            $result['status'] = 'free';
//        }
        $result['status'] = $cacheMan->getTableStatusCache();
        if($result['status'] == 'busy'){
            $result['player1'] = $users[array_rand($users)];
            $result['player2'] = $users[array_rand($users)];
            $result['player3'] = $users[array_rand($users)];
            $result['player4'] = $users[array_rand($users)];
            $result['scoreWhite'] = $cacheMan->getScoreCache()['score']['white'];
            $result['scoreBlack'] = $cacheMan->getScoreCache()['score']['black'];
        }

        $response = new Response(json_encode($result));
        return $response;
    }
}

<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
        //$liveScoreManager = $this->get('kdsm_content.live_score_manager');
        //$tableStatusResponse = $liveScoreManager->getTableStatus();

        $rand = rand(1,10);
        $users = array(125234243, 135513113, 643434232, 533435335, 234234236);
        $result = array();
        if($rand <= 5) {
            $result['status'] = 'free';
        }
        if($rand > 3){
            $result['status'] = 'busy';
            $result['player1'] = $users[array_rand($users)];
            $result['player2'] = $users[array_rand($users)];
            $result['player3'] = $users[array_rand($users)];
            $result['player4'] = $users[array_rand($users)];
            $result['scoreWhite'] = rand(0,10);
            $result['scoreBlack'] = rand(5,10);
        }

        $response = new Response(json_encode($result));
        return $response;
    }
}

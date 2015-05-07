<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

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
}

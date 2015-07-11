<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Helper\SuggestionsScorer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SuggestionsController extends Controller {

    /**
     * @todo lazyloading
     * @todo route url
     * @Route("/user/suggestions", name="user_get_suggestions")
     */
    public function suggestionsAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userShows = $user->getTvShows();

        $similarShows = array();
        $ignoreIds = array();
        foreach ($userShows as $userShow) {
            $ignoreIds[] = $userShow->getId();
            $similarShows[] = array(
                'show' => $userShow,
                'similar' => $userShow->getSimilarTvShows()
            );
        }

        $suggestionsScorer = new SuggestionsScorer($similarShows, $ignoreIds);
        return $this->render(
            'SunnerbergSimilarSeriesBundle:User:suggestions.html.twig',
            array('shows' => $suggestionsScorer->getGradedShows(20))
        );
    }

}
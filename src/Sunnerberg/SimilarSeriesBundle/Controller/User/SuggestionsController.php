<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Helper\SuggestionsScorer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SuggestionsController extends Controller {

    /**
     * @todo lazyloading
     * @todo route url
     * @Route("/user/suggestions/{page}", name="user_get_suggestions")
     */
    public function suggestionsAction($page = 1)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userShows = $user->getTvShows();

        $similarShows = [];
        $ignoreIds = [];
        foreach ($userShows as $userShow) {
            $ignoreIds[] = $userShow->getId();
            $similarShows[] = [
                'show' => $userShow,
                'similar' => $userShow->getSimilarTvShows()
            ];
        }

        $tmdbPosterHelper = $this->get('sunnerberg_similar_series.helper.tmdb_poster_helper');
        $posterBaseUrl = $tmdbPosterHelper->getPosterBaseUrl(1);

        $suggestionsScorer = new SuggestionsScorer($similarShows, $ignoreIds);

        return new JsonResponse(array(
            'suggestions' => $suggestionsScorer->getGradedSuggestions(20),
            'posterBaseUrl' => $posterBaseUrl
        ));
    }

}
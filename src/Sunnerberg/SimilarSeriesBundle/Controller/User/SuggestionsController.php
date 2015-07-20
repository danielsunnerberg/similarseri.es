<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Helper\SuggestionsScorer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SuggestionsController extends Controller {

    /**
     * @Route("/user/suggestions/{offset}/{limit}", name="user_get_suggestions", methods={"get"})
     */
    public function suggestionsAction($offset = 0, $limit = 20)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userShows = $user->getTvShows();

        $similarShows = [];
        $ignoreIds = [];
        foreach ($userShows as $userShow) {
            // We dont want to suggest a show that the user already has seen
            $ignoreIds[] = $userShow->getId();

            $similarShows[] = [
                'show' => $userShow,
                'similar' => $userShow->getSimilarTvShows()
            ];
        }

        foreach ($user->getIgnoredTvShows() as $ignoredShow) {
            $ignoreIds[] = $ignoredShow->getId();
        }

        $tmdbPosterHelper = $this->get('sunnerberg_similar_series.helper.tmdb_poster_helper');
        $posterBaseUrl = $tmdbPosterHelper->getPosterBaseUrl(1);

        $suggestionsScorer = new SuggestionsScorer($similarShows, $ignoreIds);
        $suggestions = $suggestionsScorer->getGradedSuggestions($offset, $limit);
        foreach ($suggestions as $suggestion) {
            $suggestion->getShow()->injectPosterBaseUrl($posterBaseUrl);
        }

        return new JsonResponse(array(
            'suggestions' => $suggestions,
            'hasMoreSuggestions' => $suggestionsScorer->hasMoreSuggestions($offset, $limit)
        ));
    }

}

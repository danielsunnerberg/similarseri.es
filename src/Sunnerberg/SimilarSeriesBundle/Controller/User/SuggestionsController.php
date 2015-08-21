<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Helper\SuggestionsScorer;
use Sunnerberg\SimilarSeriesBundle\Helper\TmdbPosterSize;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SuggestionsController extends Controller {

    /**
     * @Route("/user/suggestions/{offset}/{limit}", name="user_get_suggestions", methods={"get"})
     */
    public function suggestionsAction($offset = 0, $limit = 20)
    {
        $user = $this->getUser();

        $tmdbPosterHelper = $this->get('sunnerberg_similar_series.helper.tmdb_poster_helper');
        $posterBaseUrl = $tmdbPosterHelper->getPosterBaseUrl(TmdbPosterSize::W154);

        $suggestionsScorer = new SuggestionsScorer(
            $user->getTvShows()->toArray(),
            $user->getIgnoredTvShows()->toArray()
        );
        $suggestions = $suggestionsScorer->getGradedSuggestions($offset, $limit);
        foreach ($suggestions as $suggestion) {
            $suggestion->getShow()->injectPosterBaseUrl($posterBaseUrl);
        }

        return new JsonResponse([
            'suggestions' => $suggestions,
            'hasMoreSuggestions' => $suggestionsScorer->hasMoreSuggestions($offset, $limit)
        ]);
    }

}

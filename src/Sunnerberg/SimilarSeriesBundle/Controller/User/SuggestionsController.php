<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Entity\Suggestion;
use Sunnerberg\SimilarSeriesBundle\Helper\SuggestionsScorer;
use Sunnerberg\SimilarSeriesBundle\Helper\TmdbPosterSize;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SuggestionsController extends Controller {

    /**
     * @Route("/user/suggestions/{offset}/{limit}/{popularFallback}", name="user_get_suggestions", methods={"get"})
     * @param int $offset
     * @param int $limit
     * @param boolean $popularFallback if no suggestions can be found, should popular suggestions be used instead
     * @return JsonResponse
     */
    public function suggestionsAction($offset = 0, $limit = 20, $popularFallback = false)
    {
        $tmdbPosterHelper = $this->get('sunnerberg_similar_series.helper.tmdb_poster_helper');
        $posterBaseUrl = $tmdbPosterHelper->getPosterBaseUrl(TmdbPosterSize::W342);

        $user = $this->getUser();
        $suggestionsScorer = new SuggestionsScorer(
            $user->getTvShows()->toArray(),
            $user->getIgnoredTvShows()->toArray()
        );
        $suggestions = $suggestionsScorer->getGradedSuggestions($offset, $limit);

        $fallbackUsed = false;
        if (empty($suggestions) && filter_var($popularFallback, FILTER_VALIDATE_BOOLEAN)) {
            $suggestions = $this->suggestPopularShows();
            $fallbackUsed = true;
        }
        foreach ($suggestions as $suggestion) {
            $suggestion->getShow()->injectPosterBaseUrl($posterBaseUrl);
        }

        return new JsonResponse([
            'suggestions' => $suggestions,
            'hasMoreSuggestions' => $suggestionsScorer->hasMoreSuggestions($offset, $limit),
            'fallbackUsed' => $fallbackUsed
        ]);
    }

    private function suggestPopularShows()
    {
        $showRepository = $this->getDoctrine()->getRepository('SunnerbergSimilarSeriesBundle:TvShow');
        $shows = $showRepository->findBy([], ['popularity' => 'DESC'], 21);

        $suggestions = [];
        foreach ($shows as $show) {
            $suggestions[] = new Suggestion($show);
        }

        return $suggestions;
    }

}

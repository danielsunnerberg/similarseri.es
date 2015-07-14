<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tmdb\Model\Tv\QueryParameter\AppendToResponse;
use Tmdb\Model\Search\SearchQuery\TvSearchQuery;

class SearchController extends Controller {

    /**
     * @Route("/search/{query}.json", name="search_route", methods={"get"})
     */
    public function searchAction($query)
    {
        $searchRepository = $this->get('tmdb.search_repository');
        $filters = new TvSearchQuery();
        $response = $searchRepository->searchTv($query, $filters)->getAll();
        $tmdbPosterHelper = $this->get('sunnerberg_similar_series.helper.tmdb_poster_helper');
        $posterBaseUrl = $tmdbPosterHelper->getPosterBaseUrl(0);

        $matchingShows = [];
        foreach ($response as $show) {
            $matchingShows[] = [
                'tmdbId' => $show->getId(),
                'name' => $show->getName(),
                'airYear' => $show->getFirstAirDate()->format('Y'),
                'posterUrl' => $posterBaseUrl . $show->getPosterPath(),
            ];
        }

        return new JsonResponse($matchingShows);
    }

}
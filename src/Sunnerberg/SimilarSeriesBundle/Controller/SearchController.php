<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Helper\TmdbPosterSize;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tmdb\Model\Search\SearchQuery\TvSearchQuery;

class SearchController extends Controller {

    /**
     * @Route("/search/{query}.json", name="search_route", methods={"get"})
     */
    public function searchAction($query)
    {
        $cache = $this->get('cache');
        $queryCacheId = sprintf('similarseries.search.%s', $query);
        $cachedSearch = $cache->fetch($queryCacheId);
        if ($cachedSearch) {
            return new JsonResponse($cachedSearch);
        }

        $searchRepository = $this->get('tmdb.search_repository');
        $filters = new TvSearchQuery();
        $filters->searchType('ngram');
        $shows = $this->formatSearchResponse(
            $searchRepository->searchTv($query, $filters)->getAll()
        );

        $cache->save($queryCacheId, $shows, 60 * 60 * 24 * 30);

        return new JsonResponse($shows);
    }

    private function formatSearchResponse($response)
    {
        $tmdbPosterHelper = $this->get('sunnerberg_similar_series.helper.tmdb_poster_helper');
        $posterBaseUrl = $tmdbPosterHelper->getPosterBaseUrl(TmdbPosterSize::W92);

        $matchingShows = [];
        foreach ($response as $show) {
            if ($show->getPosterPath() === null) {
                continue;
            }
            $matchingShows[] = [
                'tmdbId' => $show->getId(),
                'name' => $show->getName(),
                'airYear' => $show->getFirstAirDate()->format('Y'),
                'posterUrl' => $posterBaseUrl . $show->getPosterImage()->getPath(),
            ];
        }
        return $matchingShows;
    }

}

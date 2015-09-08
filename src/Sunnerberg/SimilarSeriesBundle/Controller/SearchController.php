<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Helper\Image\TmdbPosterSize;
use Sunnerberg\SimilarSeriesBundle\Helper\TmdbShowValidator;
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
        $tmdbImageHelper = $this->get('sunnerberg_similar_series.helper.tmdb_image_helper');
        $posterBaseUrl = $tmdbImageHelper->getImageBaseUrl(TmdbPosterSize::W92);

        $matchingShows = [];
        $qualityValidator = new TmdbShowValidator();
        foreach ($response as $show) {

            if (! $qualityValidator->isValid($show)) {
                continue;
            }

            $matchingShows[] = [
                'tmdbId' => $show->getId(),
                'name' => $show->getName(),
                'airYear' => $show->getFirstAirDate()->format('Y'),
                'posterUrl' => $posterBaseUrl . $show->getPosterPath(),
            ];
        }
        return $matchingShows;
    }

}

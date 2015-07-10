<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tmdb\Model\Tv\QueryParameter\AppendToResponse;
use Tmdb\Model\Search\SearchQuery\TvSearchQuery;

class SearchController extends Controller {

    /**
     * @Route("/search/{query}.json", name="search_route")
     */
    public function searchAction($query)
    {
        $searchRepository = $this->get('tmdb.search_repository');
        $filters = new TvSearchQuery();
        $response = $searchRepository->searchTv($query, $filters)->getAll();
        $posterBase = $this->getPosterBaseUrl();

        $matchingShows = array();
        foreach ($response as $show) {
            $matchingShows[] = array(
                'tmdbId' => $show->getId(),
                'name' => $show->getName(),
                'airYear' => $show->getFirstAirDate()->format('Y'),
                'posterUrl' => $posterBase . $show->getPosterPath(),
            );
        }

        return new JsonResponse($matchingShows);
    }

    private function getPosterBaseUrl()
    {
        // @todo cache this
        $configurationRepository = $this->get('tmdb.configuration_repository');
        $tmdbConfig = $configurationRepository->load();
        $imageConfig = $tmdbConfig->getImages();
        return $imageConfig['secure_base_url'] . $imageConfig['poster_sizes'][0];
    }

}
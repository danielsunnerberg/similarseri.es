<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Tmdb\Model\Search\SearchQuery\TvSearchQuery;

class SearchController extends Controller {

    /**
     * @Route("/search/{query}", name="search_route")
     */
    public function searchAction($query)
    {
        $searchRepository = $this->get('tmdb.search_repository');

        $filters = new TvSearchQuery(); // @todo should be able to add 'similar' to result here too
        $matches = $searchRepository->searchTv($query, $filters)->getAll();
        if (empty($matches)) {
            throw new InvalidArgumentException("Found no matches.");
        }
        $match = reset($matches);

        $tvRepository = $this->get('tmdb.tv_repository');
        $match = $tvRepository->load($match->getId());
        $similar = $match->getSimilar()->getAll();

        $data = array(
            'poster_base_url' => $this->getImageBaseUrl(),
            'match' => $match,
            'similar' => $similar
        );

        return $this->render('SunnerbergSimilarSeriesBundle:Default:index.html.twig', $data);
    }

    private function getImageBaseUrl()
    {
        // @todo cache this
        $configurationRepository = $this->get('tmdb.configuration_repository');
        $tmdbConfig = $configurationRepository->load();
        $imageConfig = $tmdbConfig->getImages();
        return $imageConfig['secure_base_url'] . $imageConfig['poster_sizes'][2];
    }

}
<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Tmdb\Model\Search\SearchQuery\TvSearchQuery;

class DefaultController extends Controller
{

//    /**
//     * @Route("/app/awd/{name}", name="start")
//     */
//    public function indexAction($name)
//    {
//        return $this->render('SunnerbergSimilarSeriesBundle:Default:index.html.twig', array('name' => $name));
//    }

    /**
     * @Route("/", name="start")
     */
    public function indexAction()
    {
        $searchRepository = $this->get('tmdb.search_repository');

        $query = new TvSearchQuery(); // @todo should be able to add 'similar' to result here too
        $matches = $searchRepository->searchTv("Orange is the new black", $query)->getAll();
        if (empty($matches)) {
            throw new InvalidArgumentException("Found no matches.");
        }
        $match = reset($matches);

        $tvRepository = $this->get('tmdb.tv_repository');
        $match = $tvRepository->load($match->getId());
        \Doctrine\Common\Util\Debug::dump($match); die();
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

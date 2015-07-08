<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Tmdb\Model\Search\SearchQuery\TvSearchQuery;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="start")
     */
    public function indexAction()
    {

    }

}

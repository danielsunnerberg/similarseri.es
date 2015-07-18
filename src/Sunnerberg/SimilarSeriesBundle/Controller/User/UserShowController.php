<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tmdb\Model\Tv;

class UserShowController extends Controller {

    /**
     * @Route("/user/shows/{tmdbId}", name="user_add_show", methods={"put", "post"})
     */
    public function addShowAction($tmdbId)
    {
        $doctrine = $this->getDoctrine();
        $tvShow = $doctrine->getRepository('SunnerbergSimilarSeriesBundle:TvShow')->getByTmdbId($tmdbId);

        $tmdbShowFetcher = $this->get('sunnerberg_similar_series.fetcher.tmdb_show_fetcher');
        if ($tvShow) {
            $tmdbShowFetcher->syncSimilarShows($tvShow, $tvShow->getTmdbId());
        } else {
            $tvShow = $tmdbShowFetcher->fetch($tmdbId);
        }
        $this->getDoctrine()->getManager()->persist($tvShow);

        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (! $user->hasTvShow($tvShow)) {
            $user->addTvShow($tvShow);
            $doctrine->getManager()->persist($user);
        }

        $doctrine->getManager()->flush();
        return new JsonResponse(true);
    }

}
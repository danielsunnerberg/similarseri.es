<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserShowController extends Controller {

    /**
     * @Route("/user/shows/{tmdbId}", name="user_add_show", methods={"post", "put"})
     */
    public function addShowAction($tmdbId)
    {
        $doctrine = $this->getDoctrine();
        $tmdbShowFetcher = $this->get('sunnerberg_similar_series.fetcher.tmdb_show_fetcher');

        $tvShow = $doctrine->getRepository('SunnerbergSimilarSeriesBundle:TvShow')->getByTmdbId($tmdbId);
        if ($tvShow) {
            if ($tvShow->getLastSyncDate() === null || $tvShow->getDaysSinceLastSync() > 90) {
                $tmdbShowFetcher->syncSimilarShows($tvShow);
            }
        } else {
            $tvShow = $tmdbShowFetcher->fetch($tmdbId);
        }

        $user = $this->getUser();
        if (! $user->hasTvShow($tvShow)) {
            $user->addTvShow($tvShow);
            $doctrine->getManager()->persist($user);
        }

        $doctrine->getManager()->flush();
        return new JsonResponse(array('success' => true));
    }

    /**
     * @Route("/user/ignored_shows/{tmdbId}", name="user_add_ignored_shows", methods={"post"})
     */
    public function addIgnoredShowAction($tmdbId)
    {
        $doctrine = $this->getDoctrine();
        $tvShow = $doctrine->getRepository('SunnerbergSimilarSeriesBundle:TvShow')->getByTmdbId($tmdbId);
        if (! $tvShow) {
            return new JsonResponse(array('success' => false), 404);
        }

        $user = $this->getUser();
        $user->addIgnoredTvShow($tvShow);
        $doctrine->getManager()->persist($user);
        $doctrine->getManager()->flush();

        return new JsonResponse(array('success' => true));
    }

}

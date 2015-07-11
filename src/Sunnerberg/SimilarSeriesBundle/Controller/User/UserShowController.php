<?php

namespace Sunnerberg\SimilarSeriesBundle\Controller\User;

use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunnerberg\SimilarSeriesBundle\Entity\Genre;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tmdb\Model\Tv;

// @todo Inject repos/services
// @todo Extract fetch/create logic: ShowFetcher -> TmdbFetcher or such
// @todo TMDB-id should likely not be stored directly on the model, but rather joined on
class UserShowController extends Controller {

    /**
     * @Route("/user/show/add/{tmdbId}", name="user_add_show")
     */
    public function addShowAction($tmdbId)
    {
        $doctrine = $this->getDoctrine();
        $tvShow = $doctrine->getRepository('SunnerbergSimilarSeriesBundle:TvShow')->getByTmdbId($tmdbId);

        if ($tvShow) {
            // @todo Refresh related shows, which includes deleting old ones
            $this->syncSimilarShows($tvShow, $this->getTmdbShowById($tvShow->getTmdbId()));
        } else {
            $tvShow = $this->downloadShow($tmdbId);
        }


        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (! $user->hasTvShow($tvShow)) {
            $user->addTvShow($tvShow);
            $doctrine->getManager()->persist($user);
        }

        $doctrine->getManager()->flush();
        return new JsonResponse(true);
    }

    private function getTmdbShowById($tmdbId)
    {
        $tmdbTvRepository = $this->get('tmdb.tv_repository');
        return $tmdbTvRepository->load($tmdbId);
    }

    private function syncSimilarShows(TvShow $tvShow, Tv $tmdbShow) {
        $tvShow->addSimilarTvShows($this->getSimilarShows($tmdbShow));
    }

    private function downloadShow($tmdbId, $processSimilarShows = true)
    {
        $tmdbShow = $this->getTmdbShowById($tmdbId);
        if (! $tmdbShow) {
            throw new NoResultException();
        }

        $tvRepository = $this->getDoctrine()->getRepository('SunnerbergSimilarSeriesBundle:TvShow');
        $genreRepository = $this->getDoctrine()->getRepository('SunnerbergSimilarSeriesBundle:Genre');
        $tvShow = $tvRepository->createFromTmdbShow($tmdbShow);
        foreach ($tmdbShow->getGenres() as $_genre) {
            $genre = $genreRepository->getOrCreateByName($_genre->getName());
            $tvShow->addGenre($genre);
        }

        if ($processSimilarShows) {
            $this->syncSimilarShows($tvShow, $tmdbShow);
        }

        $this->getDoctrine()->getManager()->persist($tvShow);
        return $tvShow;
    }

    private function getSimilarShows(Tv $tmdbShow)
    {
        $tvRepository = $this->getDoctrine()->getRepository('SunnerbergSimilarSeriesBundle:TvShow');

        $similar = array();
        foreach ($tmdbShow->getSimilar() as $_similarShow) {
            $similarShow = $tvRepository->getTvShow($_similarShow);
            if (! $similarShow) {
                $similarShow = $this->downloadShow($_similarShow->getId(), false);
            }
            $similar[] = $similarShow;
        }

        return $similar;
    }

    protected function parseQueryParameters(array $parameters = [])
    {
        foreach ($parameters as $key => $candidate) {
            if (is_a($candidate, 'Tmdb\Model\Common\QueryParameter\QueryParameterInterface')) {
                $interfaces = class_implements($candidate);

                if (array_key_exists('Tmdb\Model\Common\QueryParameter\QueryParameterInterface', $interfaces)) {
                    unset($parameters[$key]);

                    $parameters[$candidate->getKey()] = $candidate->getValue();
                }
            }
        }

        return $parameters;
    }

}
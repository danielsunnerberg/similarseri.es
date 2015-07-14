<?php

namespace Sunnerberg\SimilarSeriesBundle\Fetcher;

use Doctrine\ORM\NoResultException;
use Sunnerberg\SimilarSeriesBundle\Entity\GenreRepository;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShowRepository;
use Tmdb\Model\Tv;
use Tmdb\Repository\TvRepository;

/**
 * @TODO Future improvements:
 *  - TMDB-id should not be stored directly on the tv_shows table.
 *  - This class should be way more generic. Mainly copy-pasted and extracted from previous class.
 */
class TmdbShowFetcher extends TvShowFetcher {

    private $tmdbTvRepository;
    private $tvShowRepository;
    private $genreRepository;

    function __construct(TvRepository $tmdbTvRepository, TvShowRepository $tvShowRepository, GenreRepository $genreRepository)
    {
        $this->tmdbTvRepository = $tmdbTvRepository;
        $this->tvShowRepository = $tvShowRepository;
        $this->genreRepository = $genreRepository;
    }

    /**
     * @return Tv
     */
    private function getTmdbShowById($tmdbId)
    {
        return $this->tmdbTvRepository->load($tmdbId);
    }

    /**
     * @param $tmdbId
     * @param bool $processSimilarShows
     * @return TvShow
     * @throws NoResultException
     */
    function fetch($tmdbId, $processSimilarShows = true)
    {
        $tmdbShow = $this->getTmdbShowById($tmdbId);
        if (! $tmdbShow) {
            throw new NoResultException();
        }

        $tvShow = $this->tvShowRepository->createFromTmdbShow($tmdbShow);
        foreach ($tmdbShow->getGenres() as $_genre) {
            $genre = $this->genreRepository->getOrCreateByName($_genre->getName());
            $tvShow->addGenre($genre);
        }

        if ($processSimilarShows) {
            $this->syncSimilarShows($tvShow, $tmdbShow);
        }

        return $tvShow;
    }

    public function syncSimilarShows(TvShow $tvShow) {
        $tmdbShow = $this->getTmdbShowById($tvShow->getTmdbId());
        $tvShow->addSimilarTvShows($this->getSimilarShows($tmdbShow));
    }

    private function getSimilarShows(Tv $tmdbShow)
    {
        $similar = [];
        foreach ($tmdbShow->getSimilar() as $_similarShow) {
            $similarShow = $this->tvShowRepository->getTvShow($_similarShow);
            if (! $similarShow) {
                $similarShow = $this->fetch($_similarShow->getId(), false);
            }
            $similar[] = $similarShow;
        }

        return $similar;
    }
}
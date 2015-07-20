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

    private function extractSimilarShowIds (Tv $tmdbShow)
    {
        $similarIds = [];
        foreach ($tmdbShow->getSimilar() as $similarShow) {
            $similarIds[] = $similarShow->getId();
        }

        $totalPages = $tmdbShow->getSimilar()->getTotalPages();
        if ($totalPages === 1) {
            return $similarIds;
        }

        $api = $this->tmdbTvRepository->getApi();

        $currentPage = 2; // We've already processed page one above
        for (; $currentPage <= $totalPages; $currentPage++) {
            $similar = $api->getSimilar($tmdbShow->getId(), array('page' => $currentPage));
            foreach ($similar['results'] as $tvShow) {
                $similarIds[] = $tvShow['id'];
            }
        }

        return $similarIds;
    }

    private function getSimilarShows(Tv $tmdbShow)
    {
        // Since we can't retrieve all needed information about the related shows from the origin-show (it isn't
        // included nor supported through the API), we have to make one request per related show instead of one per
        // page.

        $similar = [];
        foreach ($this->extractSimilarShowIds($tmdbShow) as $id) {
            $similarShow = $this->tvShowRepository->getByTmdbId($id);
            if (! $similarShow) {
                $similarShow = $this->fetch($id, false);
            }
            $similar[] = $similarShow;
        }

        return $similar;
    }
}

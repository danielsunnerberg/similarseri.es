<?php

namespace Sunnerberg\SimilarSeriesBundle\Fetcher;

use Doctrine\ORM\NoResultException;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Sunnerberg\SimilarSeriesBundle\Entity\GenreRepository;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShowRepository;
use Tmdb\Model\Tv;
use Tmdb\Repository\TvRepository;

/**
 * Fetches show data from the TMDB-api.
 *
 * Class TmdbShowFetcher
 * @package Sunnerberg\SimilarSeriesBundle\Fetcher
 */
class TmdbShowFetcher extends TvShowFetcher {

    private $tmdbTvRepository;
    private $tvShowRepository;
    private $genreRepository;
    private $queueProducer;

    public function __construct(
        TvRepository $tmdbTvRepository,
        TvShowRepository $tvShowRepository,
        GenreRepository $genreRepository,
        ProducerInterface $queueProducer
    ) {
        $this->tmdbTvRepository = $tmdbTvRepository;
        $this->tvShowRepository = $tvShowRepository;
        $this->genreRepository = $genreRepository;
        $this->queueProducer = $queueProducer;
    }

    /**
     * @return Tv
     */
    private function getTmdbShowById($tmdbId)
    {
        return $this->tmdbTvRepository->load($tmdbId);
    }

    private function convertFromTmdbFormat($tmdbShow)
    {
        $tvShow = $this->tvShowRepository->createFromTmdbShow($tmdbShow);

        // If we're getting our data from the raw API, it will, annoyingly, come in the form of an array, without
        // genres.
        if (is_object($tmdbShow)) {
            foreach ($tmdbShow->getGenres() as $_genre) {
                $genre = $this->genreRepository->getOrCreateByName($_genre->getName());
                $tvShow->addGenre($genre);
            }
        }
        return $tvShow;
    }

    /**
     * @param $tmdbId
     * @param bool $processSimilarShows
     * @return TvShow
     * @throws NoResultException
     */
    public function fetch($tmdbId, $processSimilarShows = true)
    {
        $tmdbShow = $this->getTmdbShowById($tmdbId);
        if (! $tmdbShow) {
            throw new NoResultException();
        }

        $tvShow = $this->convertFromTmdbFormat($tmdbShow);

        if ($processSimilarShows) {
            $this->syncSimilarShows($tvShow, $tmdbShow);
        }

        return $tvShow;
    }

    public function syncSimilarShows(TvShow $tvShow, Tv $tmdbShow = null)
    {
        if (! $tmdbShow) {
            $tmdbShow = $this->getTmdbShowById($tvShow->getTmdbId());
        }

        $similarTvShows = $this->extractSimilarShows($tmdbShow);
        $tvShow->addSimilarTvShows($similarTvShows);
        $this->queueShowPatcher($similarTvShows);
    }

    private function queueShowPatcher(array $similarTvShows)
    {
        foreach ($similarTvShows as $similarTvShow) {
            $data = ['tmdb_id' => $similarTvShow->getTmdbId()];
            $this->queueProducer->publish(serialize($data));
        }
    }

    private function extractSimilarShows(Tv $tmdbShow)
    {
        $similarShows = [];
        foreach ($tmdbShow->getSimilar() as $similarShow) {
            $similarShows[] = $this->convertFromTmdbFormat($similarShow);
        }

        $totalPages = $tmdbShow->getSimilar()->getTotalPages();
        if ($totalPages === 1) {
            return $similarShows;
        }

        $api = $this->tmdbTvRepository->getApi();

        $currentPage = 2; // We've already processed page one above
        for (; $currentPage <= $totalPages; $currentPage++) {
            $similar = $api->getSimilar($tmdbShow->getId(), array('page' => $currentPage));
            foreach ($similar['results'] as $tvShow) {
                $similarShows[] = $this->convertFromTmdbFormat($tvShow);
            }
        }

        return $similarShows;
    }

}

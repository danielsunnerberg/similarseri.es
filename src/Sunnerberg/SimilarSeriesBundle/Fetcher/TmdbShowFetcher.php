<?php

namespace Sunnerberg\SimilarSeriesBundle\Fetcher;

use Doctrine\ORM\NoResultException;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Sunnerberg\SimilarSeriesBundle\Entity\GenreRepository;
use Sunnerberg\SimilarSeriesBundle\Entity\MediaObject;
use Sunnerberg\SimilarSeriesBundle\Entity\Person;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShowRepository;
use Sunnerberg\SimilarSeriesBundle\Helper\TmdbShowValidator;
use Tmdb\Model\Tv;
use Tmdb\Repository\TvRepository;

/**
 * Fetches show data from the TMDB-api.
 *
 * Class TmdbShowFetcherInterface
 * @package Sunnerberg\SimilarSeriesBundle\Fetcher
 */
class TmdbShowFetcher implements TvShowFetcherInterface {

    private $tmdbTvRepository;
    private $tvShowRepository;
    private $genreRepository;
    private $queueProducer;
    private $qualityValidator;

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
        $this->qualityValidator = new TmdbShowValidator();
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
        foreach ($tmdbShow->getGenres() as $_genre) {
            $genre = $this->genreRepository->getOrCreateByName($_genre->getName());
            $tvShow->addGenre($genre);
        }

        foreach ($tmdbShow->getCreatedBy() as $tmdbAuthor) {
            $author = new Person($tmdbAuthor->getName());
            $profilePath = $tmdbAuthor->getProfilePath();
            if ($profilePath) {
                $image = new MediaObject($profilePath);
                $author->setImage($image);
            }
            $tvShow->addAuthor($author);
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
        if (! $tmdbShow || ! $this->qualityValidator->isValid($tmdbShow)) {
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
        foreach ($similarTvShows as $similarShow) {
            if (! $this->qualityValidator->isValid($similarShow)) {
                continue;
            }
            $convertedShow = $this->tvShowRepository->getByTmdbId($similarShow->getId());
            if (! $convertedShow) {
                $convertedShow = $this->convertFromTmdbFormat($similarShow);
            }

            $tvShow->addSimilarTvShow($convertedShow);
            $this->queueShowPatching($similarShow);
        }

        $tvShow->setLastSyncDate(new \DateTime());
    }

    private function queueShowPatching(Tv $similarShow)
    {
        $data = ['tmdb_id' => $similarShow->getId()];
        $this->queueProducer->publish(serialize($data));
    }

    private function extractSimilarShows(Tv $tmdbShow)
    {
        $similarShows = [];
        foreach ($tmdbShow->getSimilar() as $similarShow) {
            $similarShows[] = $similarShow;
        }

        $totalPages = $tmdbShow->getSimilar()->getTotalPages();
        if ($totalPages === 1) {
            return $similarShows;
        }

        $api = $this->tmdbTvRepository->getApi();

        $currentPage = 2; // We've already processed page one above
        for (; $currentPage <= $totalPages; $currentPage++) {
            $similar = $this->tmdbTvRepository->getFactory()->createResultCollection(
                $api->getSimilar($tmdbShow->getId(), ['page' => $currentPage])
            );
            foreach ($similar as $tvShow) {
                $similarShows[] = $tvShow;
            }
        }

        return $similarShows;
    }

}

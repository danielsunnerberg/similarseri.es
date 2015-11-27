<?php

namespace Sunnerberg\SimilarSeriesBundle\Fetcher;

use Doctrine\ORM\NoResultException;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Sunnerberg\SimilarSeriesBundle\Entity\Actor;
use Sunnerberg\SimilarSeriesBundle\Entity\GenreRepository;
use Sunnerberg\SimilarSeriesBundle\Entity\MediaObject;
use Sunnerberg\SimilarSeriesBundle\Entity\Person;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShowRepository;
use Sunnerberg\SimilarSeriesBundle\Helper\TmdbShowValidator;
use Tmdb\Model\Person\CastMember;
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

    private function convertFromTmdbFormat(Tv $tmdbShow, TvShow $writeTo = null)
    {
        $tvShow = $this->tvShowRepository->createFromTmdbShow($tmdbShow, $writeTo);
        if ($tmdbShow->getGenres()) {
            $this->syncGenres($tmdbShow, $tvShow);
        }
        if ($tmdbShow->getCreatedBy()) {
            $this->syncAuthors($tmdbShow, $tvShow);
        }
        if ($tmdbShow->getCredits() && $tmdbShow->getCredits()->getCast()) {
            $this->syncActors($tmdbShow, $tvShow);
        }
        return $tvShow;
    }

    public function fetch($tmdbId, $processSimilarShows = true, TvShow $writeTo = null)
    {
        $tmdbShow = $this->getTmdbShowById($tmdbId);
        if (! $tmdbShow || ! $this->qualityValidator->isValid($tmdbShow)) {
            throw new NoResultException();
        }

        $tvShow = $this->convertFromTmdbFormat($tmdbShow, $writeTo);

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

    private function syncGenres(Tv $tmdbShow, TvShow $tvShow)
    {
        foreach ($tmdbShow->getGenres() as $tmdbGenre) {
            if ($tmdbGenre->getName() === null) {
                continue; // Sometimes, the TMDB-API includes a null-genre
            }
            $genre = $this->genreRepository->getOrCreateByName($tmdbGenre->getName());
            $tvShow->addGenre($genre);
        }
    }

    private function syncAuthors(Tv $tmdbShow, TvShow $tvShow)
    {
        foreach ($tmdbShow->getCreatedBy() as $tmdbAuthor) {
            $author = new Person($tmdbAuthor->getName());
            $this->syncPersonImage($tmdbAuthor, $author);
            $tvShow->addAuthor($author);
        }
    }

    private function syncActors(Tv $tmdbShow, TvShow $tvShow)
    {
        foreach ($tmdbShow->getCredits()->getCast()->getCast() as $tmdbActor) {
            $actor = new Actor($tmdbActor->getName());
            $actor->setCharacter($tmdbActor->getCharacter());
            $actor->setOrder($tmdbActor->getOrder());
            $this->syncPersonImage($tmdbActor, $actor);
            $tvShow->addActor($actor);
        }
    }

    private function syncPersonImage(CastMember $tmdbAuthor, Person $person)
    {
        $profilePath = $tmdbAuthor->getProfilePath();
        if ($profilePath) {
            $image = new MediaObject($profilePath);
            $person->setImage($image);
        }
    }

}

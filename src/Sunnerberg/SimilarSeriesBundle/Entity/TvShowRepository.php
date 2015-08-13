<?php

namespace Sunnerberg\SimilarSeriesBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraints\DateTime;
use Tmdb\Model\Tv;

class TvShowRepository extends EntityRepository
{

    /**
     * @param Tv $tmdbShow
     * @return null|TvShow
     */
    public function getTvShow(Tv $tmdbShow)
    {
        return $this->getByTmdbId($tmdbShow->getId());
    }

    /**
     * @param $tmdbId
     * @return null|TvShow
     */
    public function getByTmdbId($tmdbId)
    {
        return $this->findOneBy(array('tmdbId' => $tmdbId));
    }

    private function createFromTmdbShowObject(Tv $tmdbShow)
    {
        $tvShow = new TvShow();
        $tvShow->setTmdbId($tmdbShow->getId());
        $tvShow->setName($tmdbShow->getName());
        $tvShow->setPopularity($tmdbShow->getPopularity());
        $tvShow->setVoteAverage($tmdbShow->getVoteAverage());
        $tvShow->setImdbId($tmdbShow->getExternalIds()->getImdbId());
        $tvShow->setPosterUrl($tmdbShow->getPosterPath());
        $tvShow->setAirDate($tmdbShow->getFirstAirDate());
        $tvShow->setOverview($tmdbShow->getOverview());

        return $tvShow;
    }

    private function createFromTmdbShowArray($tmdbShow)
    {
        $tvShow = new TvShow();
        $tvShow->setTmdbId($tmdbShow['id']);
        $tvShow->setName($tmdbShow['name']);
        $tvShow->setPopularity($tmdbShow['popularity']);
        $tvShow->setVoteAverage($tmdbShow['vote_average']);
        $tvShow->setPosterUrl($tmdbShow['poster_path']);
        $airDate = \DateTime::createFromFormat('Y-m-d', $tmdbShow['first_air_date']);
        if ($airDate) {
            $tvShow->setAirDate($airDate);
        }
        $tvShow->setOverview($tmdbShow['overview']);

        return $tvShow;
    }

    /**
     * @param $tmdbShow Tv|array
     * @return TvShow
     */
    public function createFromTmdbShow($tmdbShow)
    {
        if (is_object($tmdbShow)) {
            return $this->createFromTmdbShowObject($tmdbShow);
        } else if (is_array($tmdbShow)) {
            return $this->createFromTmdbShowArray($tmdbShow);
        } else {
            throw new InvalidArgumentException('Argument must either be an object or an array');
        }
    }

}

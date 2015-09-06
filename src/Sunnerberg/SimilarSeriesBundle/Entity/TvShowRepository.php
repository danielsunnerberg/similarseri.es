<?php

namespace Sunnerberg\SimilarSeriesBundle\Entity;

use Doctrine\ORM\EntityRepository;
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

    /**
     * @param $tmdbShow Tv
     * @return TvShow
     */
    public function createFromTmdbShow(Tv $tmdbShow)
    {
        $tvShow = new TvShow();
        $tvShow->setTmdbId($tmdbShow->getId());
        $tvShow->setName($tmdbShow->getName());
        $tvShow->setPopularity($tmdbShow->getPopularity());
        $tvShow->setVoteAverage($tmdbShow->getVoteAverage());
        $tvShow->setVoteCount($tmdbShow->getVoteCount());
        if ($tmdbShow->getExternalIds()) {
            $tvShow->setImdbId($tmdbShow->getExternalIds()->getImdbId());
        }
        $tvShow->setPosterUrl($tmdbShow->getPosterPath());
        $tvShow->setAirDate($tmdbShow->getFirstAirDate());
        $tvShow->setOverview($tmdbShow->getOverview());

        return $tvShow;
    }

}

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

    public function createFromTmdbShow(Tv $tmdbShow)
    {
        $tvShow = new TvShow();
        $tvShow->setTmdbId($tmdbShow->getId());
        $tvShow->setName($tmdbShow->getName());
        $tvShow->setPopularity($tmdbShow->getPopularity());
        $tvShow->setVoteAverage($tmdbShow->getVoteAverage());
        $tvShow->setImdbId($tmdbShow->getExternalIds()->getImdbId());
        $tvShow->setPosterUrl($tmdbShow->getPosterPath());

        return $tvShow;
    }

}

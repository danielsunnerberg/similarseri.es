<?php

namespace Sunnerberg\SimilarSeriesBundle\Fetcher;

use Doctrine\ORM\NoResultException;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;

interface TvShowFetcherInterface {

    /**
     * @param $serviceId
     * @param bool $processSimilarShows Whether similar shows should also be fetched.
     * @param TvShow $writeTo If provided, existing instance which the fetched data will be written to.
     * @throws NoResultException
     * @return TvShow
     */
    function fetch($serviceId, $processSimilarShows = true, TvShow $writeTo = null);

}

<?php

namespace Sunnerberg\SimilarSeriesBundle\Fetcher;

use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;

interface TvShowFetcherInterface {

    /**
     * @param $serviceId
     * @param bool $processSimilarShows
     * @return TvShow
     */
    function fetch($serviceId, $processSimilarShows = true);

}

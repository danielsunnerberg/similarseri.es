<?php

namespace Sunnerberg\SimilarSeriesBundle\Fetcher;

use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;

interface TvShowFetcher {

    /**
     * @param $serviceId
     * @param bool $processSimilarShows
     * @return TvShow
     */
    function fetch($serviceId, $processSimilarShows = true);

}

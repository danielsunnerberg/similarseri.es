<?php

namespace Sunnerberg\SimilarSeriesBundle\Fetcher;

use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;

abstract class TvShowFetcher {

    /**
     * @param $serviceId
     * @param bool $processSimilarShows
     * @return TvShow
     */
    abstract function fetch($serviceId, $processSimilarShows = true);

}

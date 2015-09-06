<?php

namespace Sunnerberg\SimilarSeriesBundle\Helper;

use Tmdb\Model\Tv;

class TmdbShowValidator {

    public function isValid(Tv $show)
    {
        return self::hasValidPoster($show) && self::hasValidOverview($show);
    }

    public static function hasValidPoster(Tv $show)
    {
        return $show->getPosterPath() != null;
    }

    public static function hasValidOverview(Tv $show)
    {
        return $show->getOverview() != null;
    }

}

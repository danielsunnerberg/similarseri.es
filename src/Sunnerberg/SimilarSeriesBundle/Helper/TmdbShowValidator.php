<?php

namespace Sunnerberg\SimilarSeriesBundle\Helper;

use Tmdb\Model\Tv;

/**
 * Provides tools determining whether a show is worth fetching or not.
 * Typically, TmdbShowValidator#isValid is the function to use.
 *
 * Class TmdbShowValidator
 * @package Sunnerberg\SimilarSeriesBundle\Helper
 */
class TmdbShowValidator {

    /**
     * Checks whether the show is worth fetching by running all available validators on it.
     *
     * @param Tv $show
     * @return bool
     */
    public function isValid(Tv $show)
    {
        return self::hasValidPoster($show) && self::hasValidOverview($show);
    }

    private static function isNullOrEmpty($value)
    {
        return $value === null || empty($value);
    }

    /**
     * @param Tv $show
     * @return bool
     */
    public static function hasValidPoster(Tv $show)
    {
        return ! self::isNullOrEmpty($show->getPosterPath());
    }

    /**
     * @param Tv $show
     * @return bool
     */
    public static function hasValidOverview(Tv $show)
    {
        return ! self::isNullOrEmpty($show->getOverview());
    }

}

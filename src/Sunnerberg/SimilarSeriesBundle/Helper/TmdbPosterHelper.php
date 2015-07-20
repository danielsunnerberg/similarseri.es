<?php

namespace Sunnerberg\SimilarSeriesBundle\Helper;

use Tmdb\Repository\ConfigurationRepository;

class TmdbPosterHelper {

    private $tmdbConfigRepo;

    function __construct(ConfigurationRepository $tmdbConfigRepo)
    {
        $this->tmdbConfigRepo = $tmdbConfigRepo;
    }

    public function getPosterBaseUrl($sizeIndex)
    {
        // @todo cache this and make it pretty
        $imageConfig = $this->tmdbConfigRepo->load()->getImages();
        return $imageConfig['secure_base_url'] . $imageConfig['poster_sizes'][$sizeIndex];
    }

}

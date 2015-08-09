<?php

namespace Sunnerberg\SimilarSeriesBundle\Helper;

use Doctrine\Common\Cache\ApcCache;
use Tmdb\Repository\ConfigurationRepository;

class TmdbPosterHelper {

    private $tmdbConfigRepo;

    function __construct(ConfigurationRepository $tmdbConfigRepo, ApcCache $cache)
    {
        $this->tmdbConfigRepo = $tmdbConfigRepo;
        $this->cache = $cache;
    }

    /**
     * Returns the TMDB poster base URL, preferably from the APC cache if available.
     *
     * @param $size integer index, valid entries found in TmdbPosterSize
     * @return string
     */
    public function getPosterBaseUrl($size)
    {
        $cachedBaseUrl = $this->cache->fetch('tmdb.config.poster_base_url');
        if ($cachedBaseUrl) {
            return $cachedBaseUrl;
        }

        $imageConfig = $this->tmdbConfigRepo->load()->getImages();
        $baseUrl = $imageConfig['secure_base_url'] . $imageConfig['poster_sizes'][$size];
        $this->cache->save('tmdb.config.poster_base_url', $baseUrl, 60 * 60 * 24 * 14);

        return $baseUrl;
    }

}

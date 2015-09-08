<?php

namespace Sunnerberg\SimilarSeriesBundle\Helper\Image;

use Doctrine\Common\Cache\ApcCache;
use Tmdb\Repository\ConfigurationRepository;

class TmdbImageHelper {

    const CACHE_LIFETIME = 1209600; // 60 * 60 * 24 * 14 = 2 weeks

    private $tmdbConfigRepo;

    public function __construct(ConfigurationRepository $tmdbConfigRepo, ApcCache $cache)
    {
        $this->tmdbConfigRepo = $tmdbConfigRepo;
        $this->cache = $cache;
    }

    private function fetchResourceBaseUrl()
    {
        $cacheId = 'tmdb.config.secure_base_url';
        $cachedBaseUrl = $this->cache->fetch($cacheId);
        if ($cachedBaseUrl) {
            return $cachedBaseUrl;
        }

        $imageConfig = $this->tmdbConfigRepo->load()->getImages();
        $baseUrl = $imageConfig['secure_base_url'];
        $this->cache->save($cacheId, $baseUrl, self::CACHE_LIFETIME);

        return $baseUrl;
    }

    /**
     * Returns the TMDB image base URL, preferably from the APC cache if available.
     *
     * @param $size string, valid entries found in TmdbPosterSize, TmdbBackdropSize etc.
     * @return string
     */
    public function getImageBaseUrl($size)
    {
        return sprintf('%s%s', $this->fetchResourceBaseUrl(), $size);
    }

}

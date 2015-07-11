<?php

namespace Sunnerberg\SimilarSeriesBundle\Helper;

use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;

class SuggestionsScorer {

    const REFERRAL_SCORE = 10;
    const POPULARITY_FACTOR = 1;
    const VOTE_AVERAGE_FACTOR = 1.5;

    /**
     * @var array
     */
    private $similarShows;

    /**
     * @var array
     */
    private $gradedShows = array();

    /**
     * @var
     */
    private $ignoreIds;

    function __construct(array $similarShows, array $ignoreIds)
    {
        $this->similarShows = $similarShows;
        $this->ignoreIds = $ignoreIds;
        $this->grade();
    }

    private function grade()
    {
        foreach ($this->similarShows as $item) {
            $baseShow = $item['show'];
            foreach ($item['similar'] as $similarShow) {
                $this->processSimilarShow($baseShow, $similarShow);
            }

        }

        $this->sortShowsByScore();
    }

    private function processSimilarShow(TvShow $baseShow, TvShow $similarShow)
    {
        $similarShowId = $similarShow->getId();
        if (in_array($similarShowId, $this->ignoreIds)) {
            return;
        }

        $score = self::REFERRAL_SCORE;

        // Multiple referals should be promoted
        if (array_key_exists($similarShowId, $this->gradedShows)) {
            $this->gradedShows[$similarShowId]['score'] += $score;
            return;
        }

        $score += ($similarShow->getPopularity() * self::POPULARITY_FACTOR);
        $score += ($similarShow->getVoteAverage() * self::VOTE_AVERAGE_FACTOR);

        // @todo Should same genre be promoted?
        // @todo Should newer shows be promoted?

        $this->gradedShows[$similarShowId] = array(
            'score' => $score,
            'show' => $similarShow
        );
    }

    /**
     * @return array
     */
    public function getGradedShows()
    {
        $gradedShows = array();
        foreach ($this->gradedShows as $item) {
            $gradedShows[] = $item['show'];
        }

        return $gradedShows;
    }

    private function sortShowsByScore()
    {
        return usort($this->gradedShows, function ($a, $b) {
            return $b['score'] - $a['score'];
        });
    }

}
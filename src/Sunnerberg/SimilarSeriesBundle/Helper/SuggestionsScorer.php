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
    private $gradedShows = [];

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
        // @todo Extract array structure to object
        $similarShowId = $similarShow->getId();
        if (in_array($similarShowId, $this->ignoreIds)) {
            return;
        }

        $score = self::REFERRAL_SCORE;
        $motivation = sprintf(
            'Because you watched %s',
            $baseShow->getName()
        );

        // Multiple referrals should be promoted, however the show's popularity etc. should only be
        // awarded once.
        if (array_key_exists($similarShowId, $this->gradedShows)) {
            $score += $this->gradedShows[$similarShowId]['score'];
            $motivations = $this->gradedShows[$similarShowId]['motivations'];
        } else {
            $score += ($similarShow->getPopularity() * self::POPULARITY_FACTOR);
            $score += ($similarShow->getVoteAverage() * self::VOTE_AVERAGE_FACTOR);
            $motivations = [];
        }
        $motivations[] = $motivation;

        // @todo Should same genre be promoted?
        // @todo Should newer shows be promoted?

        $this->gradedShows[$similarShowId] = [
            'score' => $score,
            'show' => $similarShow,
            'motivations' => $motivations
        ];
    }

    /**
     * @param null|integer $limit How many suggestions that will, maximally, be returned
     * @return array Suggestions ordered by score
     */
    public function getGradedSuggestions($limit = null)
    {
        if (is_integer($limit)) {
            return array_slice($this->gradedShows, 0, $limit);
        }
        return $this->gradedShows;
    }

    private function sortShowsByScore()
    {
        return usort($this->gradedShows, function ($a, $b) {
            return $b['score'] - $a['score'];
        });
    }

}
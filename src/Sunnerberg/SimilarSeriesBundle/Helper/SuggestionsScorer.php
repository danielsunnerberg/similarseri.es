<?php

namespace Sunnerberg\SimilarSeriesBundle\Helper;

use Sunnerberg\SimilarSeriesBundle\Entity\Suggestion;
use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;

class SuggestionsScorer {

    const REFERRAL_VALUE = 10;
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

    /**
     * @param array $similarShows
     * @param array $ignoreIds Ids of already watched shows, which will not be included as suggestions
     */
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

        if (array_key_exists($similarShowId, $this->gradedShows)) {
            $suggestion = $this->gradedShows[$similarShowId];
        } else {
            $suggestion = new Suggestion($similarShow);
        }

        if ($suggestion->addReferral($baseShow)) {
            $suggestion->addScore(self::REFERRAL_VALUE, sprintf('because you watched %s', $baseShow->getName()));
        }

        $suggestion->addUniqueScore(
            'popularity',
            $similarShow->getPopularity() * self::POPULARITY_FACTOR,
            sprintf('because of the show\'s popularity (%d)', $similarShow->getPopularity())
        );

        $suggestion->addUniqueScore(
            'vote_average',
            $similarShow->getVoteAverage() * self::VOTE_AVERAGE_FACTOR,
            sprintf('because of the show\'s average vote scoring (%d)', $similarShow->getVoteAverage())
        );

        $this->gradedShows[$similarShowId] = $suggestion;
    }

    /**
     * Returns whether more suggestions are available.
     *
     * @param $offset
     * @param $limit
     * @return bool
     */
    public function hasMoreSuggestions($offset, $limit)
    {
        return count($this->gradedShows) - $offset > $limit;
    }

    /**
     * @param null|integer $offset From at which position suggestions should be selected
     * @param null|integer $limit How many suggestions that will, maximally, be returned
     * @return array Suggestions ordered by score
     */
    public function getGradedSuggestions($offset = null, $limit = null)
    {
        if (is_numeric($offset)) {
            return array_slice($this->gradedShows, $offset, $limit);
        }
        return $this->gradedShows;
    }

    private function sortShowsByScore()
    {
        return usort($this->gradedShows, function (Suggestion $a, Suggestion $b) {
            return $b->getScore() - $a->getScore();
        });
    }

}

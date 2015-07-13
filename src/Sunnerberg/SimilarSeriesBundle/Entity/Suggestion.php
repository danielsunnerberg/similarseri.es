<?php

namespace Sunnerberg\SimilarSeriesBundle\Entity;

/**
 * Suggestion
 */
class Suggestion implements \JsonSerializable
{

    /**
     * @var TvShow
     */
    private $show;

    /**
     * @var int
     */
    private $score = 0;

    /**
     * @var array
     */
    private $referrals = [];

    /**
     * @var array
     */
    private $appliedUniqueScores = [];

    /**
     * @var array
     */
    private $motivations = [];

    /**
     * @param $show TvShow to build the suggestion around
     */
    function __construct(TvShow $show)
    {
        $this->show = $show;
    }


    public function addReferral(TvShow $referral)
    {
        if (array_key_exists($referral->getId(), $this->referrals)) {
            return false;
        }

        $this->referrals[$referral->getId()] = $referral;
        return true;
    }

    /**
     * @return double
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Adds the score to the total.
     *
     * @param double $score
     * @param string $motivation optional motivation which explains the reasoning behind the scoring
     */
    public function addScore($score, $motivation = null)
    {
        $this->score += $score;
        if ($motivation) {
            $this->addMotivation(sprintf('%s %s', $score, $motivation));
        }
    }

    /**
     * Adds the score to the total if the specified unique id hasn't previously been applied to this suggestion.
     *
     * @param string $uniqueId Id which is unique to the scoring
     * @param double $score
     * @param string $motivation optional motivation which explains the reasoning behind the scoring
     * @return boolean true if the was added
     */
    public function addUniqueScore($uniqueId, $score, $motivation)
    {
        if (in_array($uniqueId, $this->appliedUniqueScores)) {
            return false;
        }

        $this->appliedUniqueScores[] = $uniqueId;
        $this->addScore($score, $motivation);
        return true;
    }

    private function addMotivation($motivation)
    {
        $this->motivations[] = $motivation;
    }

    /**
     * @return array
     */
    public function getMotivations()
    {
        return $this->motivations;
    }

    function jsonSerialize()
    {
        return [
            'show' => $this->show,
            'score' => $this->getScore(),
            'motivations' => $this->getMotivations()
        ];
    }

}

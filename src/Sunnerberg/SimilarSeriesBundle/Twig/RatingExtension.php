<?php

namespace Sunnerberg\SimilarSeriesBundle\Twig;

/**
 * Generates n.m symbols representing a rating, e.g. stars.
 *
 * Class RatingExtension
 * @package Sunnerberg\SimilarSeriesBundle\Twig
 */
class RatingExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [new \Twig_SimpleFilter(
            'rating',
            [$this, 'generateRatingOutput'],
            ['is_safe' => ['html']]
        )];
    }

    /**
     * @param double $rating e.g: 0, 3, 8.7
     * @param string $iconClassName
     * @return string generated HTML
     */
    public function generateRatingOutput($rating, $iconClassName = 'glyphicon glyphicon-star')
    {
        if (!is_numeric($rating) || $rating <= 0) {
            return '';
        }

        $output = '';
        $ratingElementsNeeded = ceil($rating);
        for ($i = 1; $i <= $ratingElementsNeeded; $i++) {
            if ($i > $rating) {
                $fillPercentage = 100 - ($i - $rating) * 100;
            } else {
                $fillPercentage = 100;
            }
            $output .= $this->generateSymbolElement($iconClassName, $fillPercentage);
        }
        return $output;
    }

    private function generateSymbolElement($iconClassName, $fillPercentage)
    {
        $template = '
            <div class="rating-wrapper">
                <div class="rating-container" style="width: %d%%">
                    <i class="%s"></i>
                </div>
            </div>
        ';
        return sprintf($template, $fillPercentage, $iconClassName);
    }


    public function getName()
    {
        return 'rating_extension';
    }
}

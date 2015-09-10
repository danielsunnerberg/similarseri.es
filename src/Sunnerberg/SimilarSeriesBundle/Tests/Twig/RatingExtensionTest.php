<?php

namespace Sunnerberg\SimilarSeriesBundle\Tests\Entity;

use Sunnerberg\SimilarSeriesBundle\Twig\RatingExtension;

class RatingExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var RatingExtension
     */
    private $ratingExtension;

    protected function setUp()
    {
        $this->ratingExtension = new RatingExtension();
    }

    private function countItems($html)
    {
        return substr_count($html, 'rating-wrapper');
    }

    public function testNItems()
    {
        for ($n = 0; $n <= 10; $n++) {
            $html = $this->ratingExtension->generateRatingOutput($n);
            $this->assertEquals($n, $this->countItems($html));
        }
    }

    public function testNegativeItem()
    {
        $this->assertEmpty($this->ratingExtension->generateRatingOutput(-1));
    }

    public function testCustomClass()
    {
        $html = $this->ratingExtension->generateRatingOutput(4, 'foo-icon');
        $this->assertContains('<i class="foo-icon"></i>', $html);
    }

}

<?php

namespace Sunnerberg\SimilarSeriesBundle\Tests\Entity;

use Sunnerberg\SimilarSeriesBundle\Helper\TmdbShowValidator;

class TmdbShowQualityValidatorTest extends \PHPUnit_Framework_TestCase
{

    private $tmdbShow;

    protected function setUp()
    {
        $this->tmdbShow = $this->getMock('\Tmdb\Model\Tv');
    }

    private function setupMock($field, $returnValue)
    {
        $this->tmdbShow->expects($this->once())
            ->method($field)
            ->will($this->returnValue($returnValue));
    }

    private function setupPosterMock($returnValue)
    {
        $this->setupMock('getPosterPath', $returnValue);
    }

    private function setupOverviewMock($returnValue)
    {
        $this->setupMock('getOverview', $returnValue);
    }

    public function testValidPoster()
    {
        $this->setupPosterMock('foo.jpg');
        $this->assertTrue(TmdbShowValidator::hasValidPoster($this->tmdbShow));
    }

    public function testInvalidPoster()
    {
        $this->setupPosterMock('');
        $this->assertFalse(TmdbShowValidator::hasValidPoster($this->tmdbShow));
    }

    public function testInvalidPoster2()
    {
        $this->setupPosterMock(null);
        $this->assertFalse(TmdbShowValidator::hasValidPoster($this->tmdbShow));
    }

    public function testValidOverview()
    {
        $this->setupOverviewMock('foo bar baz...');
        $this->assertTrue(TmdbShowValidator::hasValidOverview($this->tmdbShow));
    }

    public function testInvalidOverview()
    {
        $this->setupOverviewMock('');
        $this->assertFalse(TmdbShowValidator::hasValidOverview($this->tmdbShow));
    }

    public function testInvalidOverview2()
    {
        $this->setupOverviewMock(null);
        $this->assertFalse(TmdbShowValidator::hasValidOverview($this->tmdbShow));
    }

    public function testValidShow()
    {
        $this->setupOverviewMock('awdawdawd');
        $this->setupPosterMock('foo.jpg');

        $validator = new TmdbShowValidator();
        $this->assertTrue($validator->isValid($this->tmdbShow));
    }

}

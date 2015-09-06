<?php

namespace Sunnerberg\SimilarSeriesBundle\Tests\Entity;

use Sunnerberg\SimilarSeriesBundle\Entity\TvShow;

class TvShowTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var TvShow
     */
    private $show;

    protected function setUp()
    {
        $this->show = new TvShow();
    }

    public function testPosterImage()
    {
        $this->assertNull($this->show->getPosterImage());
    }

    public function testSyncDate()
    {
        $this->assertNull($this->show->getLastSyncDate());
        $this->assertNull($this->show->getDaysSinceLastSync());

        $yesterday = new \DateTime('yesterday');
        $this->show->setLastSyncDate($yesterday);
        $this->assertEquals($yesterday, $this->show->getLastSyncDate());
        $this->assertEquals(1, $this->show->getDaysSinceLastSync());
    }

    public function testSimilarTvShows()
    {
        $this->assertEmpty($this->show->getSimilarTvShows());

        $dummyShow1 = new TvShow();
        $dummyShow2 = new TvShow();

        $this->show->addSimilarTvShow($dummyShow1);
        $this->assertEquals(1, count($this->show->getSimilarTvShows()));

        $this->show->addSimilarTvShows([$dummyShow1, $dummyShow2]);
        $this->assertEquals(2, count($this->show->getSimilarTvShows()));

        $this->show->removeSimilarTvShow($dummyShow1);
        $this->assertEquals(1, count($this->show->getSimilarTvShows()));
        $this->show->removeSimilarTvShow($dummyShow2);
        $this->assertEmpty($this->show->getSimilarTvShows());
    }

    public function testAirDate()
    {
        $this->assertNull($this->show->getAirDate());
        $this->assertNull($this->show->getAirYear());

        $date = $this->createDummyDate();
        $this->show->setAirDate($date);
        $this->assertEquals($date, $this->show->getAirDate());
        $this->assertEquals(2015, $this->show->getAirYear());
    }

    public function testJsonSerializeable()
    {
        $this->show->setName('foo');
        $this->show->setOverview('bar');
        $this->show->setTmdbId(982);
        $this->show->setAirDate($this->createDummyDate());

        $json = json_encode($this->show);
        $restored = json_decode($json);

        $this->assertEquals('foo', $restored->name);
        $this->assertEquals('bar', $restored->overview);
        $this->assertEquals(982, $restored->tmdbId);
        $this->assertEquals(2015, $restored->airYear);
    }

    /**
     * @return \DateTime
     */
    private function createDummyDate()
    {
        return \DateTime::createFromFormat('Y-m-d', '2015-01-01');
    }


}
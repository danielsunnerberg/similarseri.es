<?php

namespace Sunnerberg\SimilarSeriesBundle\Tests\Entity;

use Sunnerberg\SimilarSeriesBundle\Entity\MediaObject;

class MediaObjectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MediaObject
     */
    private $mediaObject;

    protected function setUp()
    {
        $this->mediaObject = new MediaObject('foo.png');
    }

    public function testConstructor()
    {
        $this->setExpectedException('InvalidArgumentException');
        new MediaObject('');
        new MediaObject(null);
    }

    public function testPath()
    {
        $this->assertEquals('foo.png', $this->mediaObject->getPath());
        $this->mediaObject->setPath('bar.png');
        $this->assertEquals('bar.png', $this->mediaObject->getPath());
    }

    public function testBaseUrl()
    {
        $this->assertNull($this->mediaObject->getUrl());
        $baseUrl = 'https://foo.bar/';
        $this->mediaObject->setBaseUrl($baseUrl);

        $this->assertEquals($baseUrl . 'foo.png', $this->mediaObject->getUrl());

        $this->mediaObject->setPath('baz.png');
        $this->assertEquals($baseUrl . 'baz.png', $this->mediaObject->getUrl());

        $otherBaseUrl = 'ftp://crazy.com';
        $this->mediaObject->setBaseUrl($otherBaseUrl);
        $this->assertEquals($otherBaseUrl . 'baz.png', $this->mediaObject->getUrl());
    }


    public function testJsonSerializeable()
    {
        $this->mediaObject->setPath('foo');
        $baseUrl = 'http://bar/';
        $this->mediaObject->setBaseUrl('' . $baseUrl . '');

        $json = json_encode($this->mediaObject);
        $restored = json_decode($json);

        $this->assertEquals('foo', $restored->path);
        $this->assertEquals($baseUrl . 'foo', $restored->url);
    }

}

<?php

namespace Sunnerberg\SimilarSeriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MediaObject
 *
 * @ORM\Table(name="media_objects")
 * @ORM\Entity()
 */
class MediaObject implements \JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var string
     */
    private $baseUrl = "";

    public function __construct($path)
    {
        $this->setPath($path);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return MediaObject
     */
    public function setPath($path)
    {
        if ($path === null || empty($path) || ! is_string($path)) {
            throw new \InvalidArgumentException('The path to the resource may not be empty or null.');
        }
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set base url
     *
     * @param $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Gets the url, in the form of "base url + path".
     *
     * @return null|string
     */
    public function getUrl()
    {
        if (empty($this->baseUrl) || empty($this->path)) {
            return null;
        }

        return sprintf('%s%s', $this->baseUrl, $this->getPath());
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'path' => $this->getPath(),
            'url' => $this->getUrl(),
        ];
    }

}

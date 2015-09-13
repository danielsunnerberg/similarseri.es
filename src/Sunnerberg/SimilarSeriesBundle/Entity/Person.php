<?php

namespace Sunnerberg\SimilarSeriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Person
 *
 * @ORM\Table(name="persons")
 * @ORM\Entity()
 */
class Person
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var MediaObject
     *
     * @ORM\OneToOne(targetEntity="Sunnerberg\SimilarSeriesBundle\Entity\MediaObject", cascade={"persist"})
     * @ORM\JoinColumn(name="image_media_object_id", referencedColumnName="id")
     */
    private $image;


    public function __construct($name)
    {
        $this->name = $name;
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
     * Set name
     *
     * @param string $name
     * @return Person
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set image
     *
     * @param MediaObject $image
     * @return Person
     */
    public function setImage(MediaObject $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return MediaObject
     */
    public function getImage()
    {
        return $this->image;
    }
}

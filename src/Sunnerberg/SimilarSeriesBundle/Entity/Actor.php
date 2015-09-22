<?php

namespace Sunnerberg\SimilarSeriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Actor
 *
 * @ORM\Table(name="actors")
 * @ORM\Entity()
 */
class Actor extends Person
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="`character`", type="string", length=255)
     */
    private $character;

    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="integer", length=255)
     */
    private $order;


    public function __construct($name)
    {
        parent::__construct($name);
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
     * Set character
     *
     * @param string $character
     * @return Actor
     */
    public function setCharacter($character)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * Get character
     *
     * @return string
     */
    public function getCharacter()
    {
        return $this->character;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return Actor
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }
}

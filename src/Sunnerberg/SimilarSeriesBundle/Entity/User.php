<?php

namespace Sunnerberg\SimilarSeriesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity()
 */
class User implements UserInterface, \Serializable, AdvancedUserInterface, EquatableInterface
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
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="smallint", )
     */
    private $locked;


    /**
     * @var array
     *
     * @ORM\ManyToMany(targetEntity="Sunnerberg\SimilarSeriesBundle\Entity\TvShow", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="users_tv_shows",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tv_show_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $tvShows;

    /**
     * @var array
     *
     * @ORM\ManyToMany(targetEntity="Sunnerberg\SimilarSeriesBundle\Entity\TvShow", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="users_ignored_tv_shows",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tv_show_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $ignoredTvShows;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tvShows = new ArrayCollection();
        $this->locked = false;
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get username, or alternative username if the user is anonymously authenticated.
     *
     * @return string
     */
    public function getPrettyUsername()
    {
        if (strpos($this->username, 'anonymous_user')  !== false) {
            return 'Anonymous User';
        }
        return $this->username;
    }

    /**
     * Add tvShow
     *
     * @param TvShow $tvShow
     * @return User
     */
    public function addTvShow(TvShow $tvShow)
    {
        $this->tvShows[] = $tvShow;

        return $this;
    }

    /**
     * Remove tvShows
     *
     * @param TvShow $tvShow
     */
    public function removeTvShow(TvShow $tvShow)
    {
        $this->tvShows->removeElement($tvShow);
    }

    /**
     * Get tvShows
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTvShows()
    {
        return $this->tvShows;
    }
    
    public function hasTvShow(TvShow $tvShow)
    {
        foreach ($this->getTvShows() as $seenShow) {
            if ($tvShow->getId() === $seenShow->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    /**
     * @param string $serialized
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized);
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    /**
     * Add ignoredTvShows
     *
     * @param TvShow $ignoredTvShow
     * @return User
     */
    public function addIgnoredTvShow(TvShow $ignoredTvShow)
    {
        $this->ignoredTvShows[] = $ignoredTvShow;

        return $this;
    }

    /**
     * Remove ignoredTvShows
     *
     * @param TvShow $ignoredTvShow
     */
    public function removeIgnoredTvShow(TvShow $ignoredTvShow)
    {
        $this->ignoredTvShows->removeElement($ignoredTvShow);
    }

    /**
     * Get ignoredTvShows
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIgnoredTvShows()
    {
        return $this->ignoredTvShows;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return ! $this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Whether the account can be logged in to or not by users.
     * Doesn't apply to programmatic login.
     *
     * @param $locked
     * @return $this
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(UserInterface $user)
    {
        if (! $user instanceof User) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        return true;
    }
}

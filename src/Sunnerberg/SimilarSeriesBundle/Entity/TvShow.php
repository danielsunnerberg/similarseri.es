<?php

namespace Sunnerberg\SimilarSeriesBundle\Entity;

use Date;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TvShow
 *
 * @ORM\Table(name="tv_shows")
 * @ORM\Entity(repositoryClass="Sunnerberg\SimilarSeriesBundle\Entity\TvShowRepository")
 */
class TvShow
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
     * @var integer
     *
     * @ORM\Column(name="tmdbId", type="integer")
     */
    private $tmdbId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="popularity", type="float")
     */
    private $popularity;

    /**
     * @var float
     *
     * @ORM\Column(name="voteAverage", type="float")
     */
    private $voteAverage;

    /**
     * @var string
     *
     * @ORM\Column(name="imdbId", type="string", length=255, nullable=true)
     */
    private $imdbId;

    /**
     * @var string
     *
     * @ORM\Column(name="posterUrl", type="string", length=255, nullable=true)
     */
    private $posterUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastSyncDate", type="date")
     */
    private $lastSyncDate;

    /**
     * @var array
     *
     * @ORM\ManyToMany(targetEntity="Sunnerberg\SimilarSeriesBundle\Entity\Genre", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="tv_shows_genres",
     *     joinColumns={@ORM\JoinColumn(name="tv_show_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")}
     * )
     */
    private $genres;

    /**
     * @var array
     *
     * @ORM\ManyToMany(targetEntity="Sunnerberg\SimilarSeriesBundle\Entity\TvShow", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="similar_tv_shows",
     *      joinColumns={@ORM\JoinColumn(name="show_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="similar_show_id", referencedColumnName="id")}
     * )
     **/
    private $similarTvShows;

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
     * @return TvShow
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
     * Set popularity
     *
     * @param float $popularity
     * @return TvShow
     */
    public function setPopularity($popularity)
    {
        $this->popularity = $popularity;

        return $this;
    }

    /**
     * Get popularity
     *
     * @return float 
     */
    public function getPopularity()
    {
        return $this->popularity;
    }

    /**
     * Set voteAverage
     *
     * @param float $voteAverage
     * @return TvShow
     */
    public function setVoteAverage($voteAverage)
    {
        $this->voteAverage = $voteAverage;

        return $this;
    }

    /**
     * Get voteAverage
     *
     * @return float 
     */
    public function getVoteAverage()
    {
        return $this->voteAverage;
    }

    /**
     * Set imdbId
     *
     * @param string $imdbId
     * @return TvShow
     */
    public function setImdbId($imdbId)
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    /**
     * Get imdbId
     *
     * @return string 
     */
    public function getImdbId()
    {
        return $this->imdbId;
    }

    /**
     * Set posterUrl
     *
     * @param string $posterUrl
     * @return TvShow
     */
    public function setPosterUrl($posterUrl)
    {
        $this->posterUrl = $posterUrl;

        return $this;
    }

    /**
     * Get posterUrl
     *
     * @return string 
     */
    public function getPosterUrl()
    {
        return $this->posterUrl;
    }

    /**
     * Set lastSyncDate
     *
     * @param \DateTime $lastSyncDate
     * @return TvShow
     */
    public function setLastSyncDate($lastSyncDate)
    {
        $this->lastSyncDate = $lastSyncDate;

        return $this;
    }

    /**
     * Get lastSyncDate
     *
     * @return \DateTime 
     */
    public function getLastSyncDate()
    {
        return $this->lastSyncDate;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lastSyncDate = new \DateTime();
        $this->genres = new ArrayCollection();
    }

    /**
     * Add genres
     *
     * @param Genre $genre
     * @return TvShow
     */
    public function addGenre(Genre $genre)
    {
        $this->genres[] = $genre;

        return $this;
    }

    /**
     * Remove genres
     *
     * @param Genre $genre
     */
    public function removeGenre(Genre $genre)
    {
        $this->genres->removeElement($genre);
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set tmdbId
     *
     * @param integer $tmdbId
     * @return TvShow
     */
    public function setTmdbId($tmdbId)
    {
        $this->tmdbId = $tmdbId;

        return $this;
    }

    /**
     * Get tmdbId
     *
     * @return integer 
     */
    public function getTmdbId()
    {
        return $this->tmdbId;
    }

    /**
     * Add similarTvShows
     *
     * @param \Sunnerberg\SimilarSeriesBundle\Entity\TvShow $similarTvShow
     * @return TvShow
     */
    public function addSimilarTvShow(TvShow $similarTvShow)
    {
        $this->similarTvShows[] = $similarTvShow;

        return $this;
    }

    /**
     * Remove similarTvShows
     *
     * @param \Sunnerberg\SimilarSeriesBundle\Entity\TvShow $similarTvShow
     */
    public function removeSimilarTvShow(TvShow $similarTvShow)
    {
        $this->similarTvShows->removeElement($similarTvShow);
    }

    /**
     * Get similarTvShows
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSimilarTvShows()
    {
        return $this->similarTvShows;
    }

    /**
     * Add similar tv shows
     *
     * @param array $getSimilarShows
     */
    public function addSimilarTvShows(array $similarTvShows)
    {
        foreach ($similarTvShows as $similarTvShow) {
            $this->addSimilarTvShow($similarTvShow);
        }
    }
}

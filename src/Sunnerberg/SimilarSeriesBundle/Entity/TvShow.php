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
class TvShow implements \JsonSerializable
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
     * @ORM\Column(name="tmdb_id", type="integer", unique=true)
     */
    private $tmdbId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="air_date", type="datetime", nullable=true)
     */
    private $airDate;

    /**
     * @var string
     *
     * @ORM\Column(name="overview", type="text", nullable=true)
     */
    private $overview;

    /**
     * @var float
     *
     * @ORM\Column(name="popularity", type="float")
     */
    private $popularity;

    /**
     * @var float
     *
     * @ORM\Column(name="vote_average", type="float")
     */
    private $voteAverage;

    /**
     * @var integer
     *
     * @ORM\Column(name="vote_count", type="integer")
     */
    private $voteCount;

    /**
     * @var string
     *
     * @ORM\Column(name="imdb_id", type="string", length=255, nullable=true)
     */
    private $imdbId;


    /**
     * @var MediaObject
     *
     * @ORM\OneToOne(targetEntity="MediaObject", cascade={"persist"})
     * @ORM\JoinColumn(name="poster_media_object_id", referencedColumnName="id")
     */
    private $posterImage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_sync_date", type="date", nullable=true)
     */
    private $lastSyncDate = null;

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
     * Constructor
     */
    public function __construct()
    {
        $this->lastSyncDate = null;
        $this->genres = new ArrayCollection();
        $this->similarTvShows = new ArrayCollection();
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
     * Set voteCount
     *
     * @param integer $voteCount
     * @return TvShow
     */
    public function setVoteCount($voteCount)
    {
        $this->voteCount = $voteCount;

        return $this;
    }

    /**
     * Get voteCount
     *
     * @return integer
     */
    public function getVoteCount()
    {
        return $this->voteCount;
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
     * Set poster image
     *
     * @param string $posterImage
     * @return TvShow
     */
    public function setPosterImage($posterImage)
    {
        $this->posterImage = $posterImage;

        return $this;
    }

    /**
     * Get poster image
     *
     * @return string 
     */
    public function getPosterImage()
    {
        return $this->posterImage;
    }

    /**
     * Set lastSyncDate
     *
     * @param \DateTime $lastSyncDate
     * @return TvShow
     */
    public function setLastSyncDate(\DateTime $lastSyncDate)
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
     * Get days since last sync date
     *
     * @return int
     */
    public function getDaysSinceLastSync()
    {
        if ($this->lastSyncDate === null) {
            return null;
        }

        return $this->lastSyncDate->diff(new \DateTime())->days;
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
     * Set genres
     *
     * @param $genres
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;
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

    public function hasSimilarTvShow(TvShow $similarShow)
    {
        return $this->similarTvShows->contains($similarShow);
    }

    /**
     * Add similarTvShows
     *
     * @param \Sunnerberg\SimilarSeriesBundle\Entity\TvShow $similarTvShow
     * @return TvShow
     */
    public function addSimilarTvShow(TvShow $similarTvShow)
    {
        if (! $this->hasSimilarTvShow($similarTvShow)) {
            $this->similarTvShows[] = $similarTvShow;
        }

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
     * @param array $similarTvShows
     */
    public function addSimilarTvShows(array $similarTvShows)
    {
        foreach ($similarTvShows as $similarTvShow) {
            $this->addSimilarTvShow($similarTvShow);
        }
    }

    /**
     * Set airDate
     *
     * @param \DateTime $airDate
     * @return TvShow
     */
    public function setAirDate(\DateTime $airDate)
    {
        $this->airDate = $airDate;

        return $this;
    }

    /**
     * Get airDate
     *
     * @return \DateTime 
     */
    public function getAirDate()
    {
        return $this->airDate;
    }

    /**
     * The year the show was aired the first time.
     *
     * @return integer
     */
    public function getAirYear()
    {
        if ($this->airDate === null) {
            return null;
        }
        return $this->airDate->format('Y');
    }

    /**
     * Set overview
     *
     * @param string $overview
     * @return TvShow
     */
    public function setOverview($overview)
    {
        $this->overview = $overview;

        return $this;
    }

    /**
     * Get overview
     *
     * @return string 
     */
    public function getOverview()
    {
        return $this->overview;
    }

    function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'airYear' => $this->getAirYear(),
            'overview' => $this->getOverview(),
            'imdbId' => $this->getImdbId(),
            'tmdbId' => $this->getTmdbId(),
            'posterImage' => $this->getPosterImage()
        ];
    }
}

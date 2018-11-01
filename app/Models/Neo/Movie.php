<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * App\Models\Neo\Movie
 *
 * @OGM\Node (label="Movie")
 */

class Movie
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="int")
     * @var int
     */
    protected $movieId;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $movieName;

    /**
     * @var Collection
     *
     * @OGM\Relationship(relationshipEntity="WatchMovie", type="WATCH_MOVIE", direction="INCOMING", collection=true, mappedBy="movie")
     */
    protected $watchMovie;

    /**
     * Movie constructor.
     */
    public function __construct()
    {
        $this->watchMovie = new Collection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getMovieId(): int
    {
        return $this->movieId;
    }

    /**
     * @param int $movieId
     */
    public function setAmount($movieId)
    {
        $this->movieId = $movieId;
    }

    /**
     * @return string
     */
    public function getMovieName(): string
    {
        return $this->movieName;
    }

    /**
     * @param string $movieName
     */
    public function setMovieName($movieName)
    {
        $this->movieName = $movieName;
    }

    /**
     * @return WatchMovie
     */
    public function getHasTransaction(): WatchMovie
    {
        return $this->watchMovie;
    }

    /**
     * @param $watchMovie
     */
    public function setHasTransaction($watchMovie)
    {
        $this->watchMovie = $watchMovie;
    }
}

<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * App\Models\Neo\WatchMovie
 *
 * @OGM\RelationshipEntity (type="WATCH_MOVIE")
 */
class WatchMovie
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @var User
     *
     * @OGM\StartNode(targetEntity="User")
     */
    protected $user;

    /**
     * @var Movie
     *
     * @OGM\EndNode(targetEntity="Movie")
     */
    protected $movie;

    public function __construct(User $user, Movie $movie)
    {
        $this->user = $user;
        $this->movie = $movie;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getMovie(): Movie
    {
        return $this->movie;
    }
}

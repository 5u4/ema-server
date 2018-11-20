<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * App\Models\Neo\Review
 *
 * @OGM\Node (label="Review")
 */
class Review
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $reviewContent;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $reviewTitle;

    /**
     * @OGM\Property(type="int")
     * @var int
     */
    protected $userId;

    /**
     * @OGM\Relationship(type="HAS_REVIEW", direction="INCOMING", collection=true, mappedBy="movies", targetEntity="Movie")
     * @var Movie[]|Collection
     */
    protected $movies;

    public function __construct()
    {
        $this->movies = new Collection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getReviewContent(): string
    {
        return $this->reviewContent;
    }

    /**
     * @param string $reviewContent
     */
    public function setReviewContent(string $reviewContent): void
    {
        $this->reviewContent = $reviewContent;
    }

    /**
     * @return string
     */
    public function getReviewTitle(): string
    {
        return $this->reviewTitle;
    }

    /**
     * @param string $reviewTitle
     */
    public function setReviewTitle(string $reviewTitle): void
    {
        $this->reviewContent = $reviewTitle;
    }
}

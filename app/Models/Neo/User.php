<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * App\Models\Neo\User
 *
 * @OGM\Node (label="User")
 */
class User
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
    protected $sqlId;

    /**
     * @var Collection
     *
     * @OGM\Relationship(relationshipEntity="HasTransaction", type="HAS_TRANSACTION", direction="OUTGOING", collection=true, mappedBy="user")
     */
    protected $hasTransaction;

    /**
     * @OGM\Relationship(type="HAS_TAG", direction="OUTGOING", collection=true, mappedBy="users", targetEntity="Tag")
     * @var Tag[]|Collection
     */
    protected $tags;

    /**
     * @OGM\Relationship(type="FOLLOW", direction="INCOMING", collection=true, mappedBy="followings", targetEntity="User")
     * @var User[]|Collection
     */
    protected $followers;

    /**
     * @OGM\Relationship(type="FOLLOW", direction="OUTGOING", collection=true, mappedBy="followers", targetEntity="User")
     * @var User[]|Collection
     */
    protected $followings;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->hasTransaction = new Collection();
        $this->followers = new Collection();
        $this->followings = new Collection();
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
    public function getSqlId(): int
    {
        return $this->sqlId;
    }

    /**
     * @param int $sqlId
     */
    public function setSqlId($sqlId)
    {
        $this->sqlId = $sqlId;
    }

    /**
     * @return Collection
     */
    public function getHasTransaction(): Collection
    {
        return $this->hasTransaction;
    }

    /**
     * @param $hasTransaction
     */
    public function setHasTransaction($hasTransaction)
    {
        $this->hasTransaction = $hasTransaction;
    }

    /**
     * @return Tag[]|Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return User[]|Collection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @return User[]|Collection
     */
    public function getFollowings()
    {
        return $this->followings;
    }
}

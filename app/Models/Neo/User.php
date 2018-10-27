<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @OGM\Node(label="User")
 * Class User
 * @package App\Models\Neo
 */
class User extends Model
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
     * @OGM\Relationship(relationshipEntity="HasTransaction", type="HAS_TRANSACTION", direction="OUTGOING", collection="true", mappedBy="user")
     */
    protected $hasTransaction;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->hasTransaction = new Collection();
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
}

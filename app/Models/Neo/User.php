<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Neo\User
 *
 * @OGM\Node (label="User")
 * Class User
 * @package App\Models\Neo
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $last_login
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Neo\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Neo\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Neo\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Neo\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Neo\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Neo\User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Neo\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Neo\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Neo\User whereUsername($value)
 * @mixin \Eloquent
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

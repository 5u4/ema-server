<?php

namespace App\Models\Neo;

use Illuminate\Database\Eloquent\Model;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 *
 * @OGM\RelationshipEntity(type="HAS_TRANSACTION")
 */
class HasTransaction extends Model
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
     * @var Transaction
     *
     * @OGM\EndNode(targetEntity="Transaction")
     */
    protected $transaction;


    public function __construct(User $user, Transaction $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}

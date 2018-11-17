<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * App\Models\Neo\Transaction
 *
 * @OGM\Node (label="Transaction")
 */
class Transaction
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="float")
     * @var float
     */
    protected $amount;

    /**
     * @OGM\Property(type="int")
     * @var int
     */
    protected $timestamp;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $description;

    /**
     * @var Collection
     *
     * @OGM\Relationship(relationshipEntity="HasTransaction", type="HAS_TRANSACTION", direction="INCOMING", collection=true, mappedBy="transaction")
     */
    protected $hasTransaction;

    /**
     * Transaction constructor.
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
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return HasTransaction
     */
    public function getHasTransaction(): HasTransaction
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

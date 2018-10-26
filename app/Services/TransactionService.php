<?php

namespace App\Services;

use App\Models\Neo\Transaction;
use GraphAware\Neo4j\OGM\EntityManager;

class TransactionService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create($amount, $description)
    {
        $transaction = new Transaction();

        $transaction->setAmount($amount);
        $transaction->setDescription($description);
        $transaction->setTimestamp(date("Y-m-d"), time());

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        // this is for json response
        $transaction->amount = $transaction->getAmount();
        $transaction->description = $transaction->getDescription();
        $transaction->timestamp = $transaction->getTimestamp();

        return $transaction;
    }
}

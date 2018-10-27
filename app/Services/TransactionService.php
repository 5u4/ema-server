<?php

namespace App\Services;

use App\Models\Neo\Transaction;
use GraphAware\Neo4j\OGM\EntityManager;
use App\Models\Neo\User;

class TransactionService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create($userId, $amount, $description)
    {
//        $transaction = new Transaction();

//        $transaction->setAmount($amount);
//        $transaction->setDescription($description);
//        $transaction->setTimestamp(date("Y-m-d"), time());
//
//        $this->entityManager->persist($transaction);
//        $this->entityManager->flush();

//        // this is for json response
//        $transaction->amount = $transaction->getAmount();
//        $transaction->description = $transaction->getDescription();
//        $transaction->timestamp = $transaction->getTimestamp();

        // use raw query to create Transaction node in Neo

        $query = "
            MERGE (u:User {sqlId: {userId}})
            MERGE (t:Transaction {amount: {amount}, description: {description}})
            CREATE (u)-[:HAS_TRANSACTION]->(t)
            RETURN t
        ";

        $transaction = null;

        $transaction = $this->entityManager->createQuery($query)
            ->setParameter('userId', $userId)
            ->setParameter('amount', $amount)
            ->setParameter('description', $description)
            ->addEntityMapping('u', User::class)
            ->addEntityMapping('t', Transaction::class)
            ->getResult();

        return $transaction;

    }
}

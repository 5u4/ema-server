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

    public function getAll($userId)
    {
        $query = "
            MATCH(u:User {sqlId: {sqlId}})-[:HAS_TRANSACTION]->(t:Transaction)
            RETURN t
        ";

        $transactions = null;

        $transactions = $this->entityManager->createQuery($query)
            ->setParameter('sqlId', $userId)
            ->addEntityMapping('u', User::class)
            ->addEntityMapping('t', Transaction::class)
            ->execute();

        return $transactions;

    }

    public function create($userId, $amount, $description)
    {
        $query = "
            MERGE (u:User {sqlId: {userId}})
            CREATE (t:Transaction {amount: {amount}, description: {description}})
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
            ->getOneResult();

        return $transaction;

    }
}

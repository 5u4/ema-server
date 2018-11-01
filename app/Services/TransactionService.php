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
            CREATE (t:Transaction {amount: {amount}, description: {description}, timestamp: {date}})
            CREATE (u)-[:HAS_TRANSACTION]->(t)
            RETURN t
        ";

        $transaction = null;

        $date = Date("Y-m-d",time());

        $transaction = $this->entityManager->createQuery($query)
            ->setParameter('userId', $userId)
            ->setParameter('amount', $amount)
            ->setParameter('date', $date)
            ->setParameter('description', $description)
            ->addEntityMapping('u', User::class)
            ->addEntityMapping('t', Transaction::class)
            ->getOneResult();

        return $transaction;

    }

    public function getTransactionById(int $id)
    {
        $query = "
            MATCH(t:Transaction)
            WHERE ID(t) = {id}
            RETURN t
        ";

        $transaction = $this->entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->addEntityMapping('t', Transaction::class)
            ->getOneResult();

        return $transaction;

    }
}

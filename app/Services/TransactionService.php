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

        // cast transaction amount to float value
        $amount = floatval($amount);

        $date = Date("Y-m-d", time());

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

    public function updateTransactionById(int $id, $amount, $description)
    {
        $query = "
            MATCH(t:Transaction)
            WHERE ID(t) = {id}
            SET t.amount = {amount}
            SET t.description = {description}
            RETURN t
        ";

        $transaction = $this->entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->setParameter('amount', $amount)
            ->setParameter('description', $description)
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

    public function deleteTransactionById(int $id)
    {
        $query = "
            MATCH(t:Transaction)-[r:HAS_TRANSACTION]-(u:User)
            WHERE ID(t) = {id}
            DELETE r
            DELETE t
        ";

        // Get the node first
        $transaction = $this->getTransactionById($id);

        // Then delete the node
        $this->entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->addEntityMapping('t', Transaction::class)
            ->execute();

        return $transaction;

    }
}

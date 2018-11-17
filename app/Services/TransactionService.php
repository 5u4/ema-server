<?php

namespace App\Services;

use App\Models\Neo\Transaction;
use GraphAware\Neo4j\OGM\EntityManager;

class TransactionService
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * TransactionService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $userId
     *
     * @return array|mixed
     */
    public function getAllTransactions(int $userId)
    {
        $query = "
            MATCH (:User {sqlId: {sqlId}})-[:HAS_TRANSACTION]->(t:Transaction)
            RETURN DISTINCT t
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('sqlId', $userId)
            ->addEntityMapping('t', Transaction::class)
            ->getResult();
    }

    /**
     * @param int $userId
     * @param float $amount
     * @param string $description
     * @param int $timestamp
     *
     * @return mixed
     */
    public function createTransaction(int $userId, float $amount, string $description, int $timestamp)
    {
        $query = "
            MATCH (u:User {sqlId: {userId}})
            CREATE (t:Transaction {amount: {amount}, description: {description}, timestamp: {timestamp}})
            CREATE (u)-[:HAS_TRANSACTION]->(t)
            RETURN t
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('userId', $userId)
            ->setParameter('amount', $amount)
            ->setParameter('description', $description)
            ->setParameter('timestamp', $timestamp)
            ->addEntityMapping('t', Transaction::class)
            ->getOneResult();
    }

    /**
     * @param int $userId
     * @param int $id
     * @param float $amount
     * @param string $description
     * @param int $timestamp
     *
     * @return mixed
     */
    public function updateTransactionById(int $userId, int $id, float $amount, string $description, ?int $timestamp)
    {
        $query = "
            MATCH (u:User {sqlId: {uid}})
            MATCH (u)-[:HAS_TRANSACTION]->(t:Transaction)
            WHERE ID(t) = {id}
            SET t.amount = {amount}
        ";

        if ($timestamp) {
            $query .= "
                SET t.timestamp = $timestamp
            ";
        }

        $query .= "
            RETURN t
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('uid', $userId)
            ->setParameter('id', $id)
            ->setParameter('amount', $amount)
            ->setParameter('description', $description)
            ->addEntityMapping('t', Transaction::class)
            ->getOneResult();
    }

    /**
     * @param int $userId
     * @param int $id
     *
     * @return mixed
     */
    public function getUserTransactionById(int $userId, int $id)
    {
        $query = "
            MATCH (u:User {sqlId: {uid}})
            MATCH (u)-[:HAS_TRANSACTION]->(t:Transaction)
            WHERE ID(t) = {id}
            RETURN t
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('uid', $userId)
            ->setParameter('id', $id)
            ->addEntityMapping('t', Transaction::class)
            ->getOneResult();
    }

    /**
     * @param int $userId
     * @param int $id
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function deleteTransactionById(int $userId, int $id)
    {
        $query = "
            MATCH (u:User {sqlId: {uid}})
            MATCH (u)-[:HAS_TRANSACTION]->(t:Transaction)
            WHERE ID(t) = {id}
            DETACH DELETE t
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('uid', $userId)
            ->setParameter('id', $id)
            ->addEntityMapping('t', Transaction::class)
            ->execute();
    }
}

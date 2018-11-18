<?php

namespace App\Services;

use App\Models\Neo\Transaction;
use GraphAware\Neo4j\OGM\EntityManager;

class TransactionService
{
    /**
     * Search support for:
     *
     * D: Mon through Sun
     * l: Sunday through Saturday
     * F: January through December
     * M: Jan through Dec
     * Y: Examples: 1999 or 2003
     * m: 01 through 12
     * d: 01 to 31
     * a: am or pm
     */
    private const DATE_SEARCH_FORMAT = 'D l F M Y m d a';

    private const TRANSACTION_SEARCH_DELIMITER = ",";

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
            ORDER BY t.timestamp DESC
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('sqlId', $userId)
            ->addEntityMapping('t', Transaction::class)
            ->getResult();
    }

    /**
     * @param array $transactions
     * @param string $fragmentString
     *
     * @return array
     */
    public function filterTransactionsWithFragments(array $transactions, string $fragmentString): array
    {
        $fragments = explode(self::TRANSACTION_SEARCH_DELIMITER, $fragmentString);

        foreach ($fragments as $fragment) {
            $transactions = array_filter($transactions, function ($transaction) use ($fragment) {
                return $this->isTransactionMatchFilter($transaction, trim($fragment));
            });
        }

        return $transactions;
    }

    /**
     * @param int $userId
     * @param float $amount
     * @param string $description
     * @param int $timestamp
     * @param array $tags
     *
     * @return mixed
     */
    public function createTransaction(int $userId, float $amount, string $description, int $timestamp, array $tags = [])
    {
        $query = "
            MATCH (u:User {sqlId: {userId}})
            CREATE (t:Transaction {amount: {amount}, description: {description}, timestamp: {timestamp}})
            CREATE (u)-[:HAS_TRANSACTION]->(t)
        ";

        foreach ($tags as $k => $tag) {
            $tag = trim($tag);

            $query .= "
                MERGE (u)-[:HAS_TAG]->(t$k:Tag {name: '$tag'})
                MERGE (t)-[:TAGGED_AS]->(t$k)
            ";
        }

        $query .= "
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
     * @param array $tags
     *
     * @return mixed
     */
    public function updateTransactionById(int $userId, int $id, float $amount, string $description, ?int $timestamp, array $tags = [])
    {
        $query = "
            MATCH (u:User {sqlId: {uid}})
            MATCH (u)-[:HAS_TRANSACTION]->(t:Transaction)
            WHERE ID(t) = {id}
            SET t.amount = {amount}
            SET t.description = {description}
        ";

        if ($timestamp) {
            $query .= "
                SET t.timestamp = $timestamp
            ";
        }

        foreach ($tags as $k => $tag) {
            $tag = trim($tag);

            $query .= "
                MERGE (u)-[:HAS_TAG]->(t$k:Tag {name: '$tag'})
                MERGE (t)-[:TAGGED_AS]->(t$k)
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

    /**
     * @param Transaction $transaction
     * @param string $fragment
     *
     * @return bool
     */
    private function isTransactionMatchFilter(Transaction $transaction, string $fragment): bool
    {
        $res = true;

        /* Handle negation */
        if ($fragment[0] == '!') {
            $res = false;
            $fragment = substr($fragment, 1);
        }

        /* Amount comparison */
        if (is_numeric($fragment) && $transaction->getAmount() == (float)$fragment) {
            return $res;
        }

        if ($fragment[0] === '=' && $transaction->getAmount() == (float)substr($fragment, 1)) {
            return $res;
        }

        if ($fragment[0] === '>') {
            $amount = substr($fragment, 1);

            if (is_numeric($amount) && $transaction->getAmount() > (float)$amount) {
                return $res;
            }
        }

        if ($fragment[0] === '<') {
            $amount = substr($fragment, 1);

            if (is_numeric($amount) && $transaction->getAmount() < (float)$amount) {
                return $res;
            }
        }

        /* Check if description contains the word */
        if (strpos(strtolower($transaction->getDescription()), $fragment) !== false) {
            return $res;
        }

        /* Check if date cointains the word */
        $dateFilterString = strtolower(date(self::DATE_SEARCH_FORMAT, $transaction->getTimestamp()));

        if (strpos($dateFilterString, strtolower($fragment)) !== false) {
            return $res;
        }

        if (strlen($fragment) <= 1) {
            return !$res;
        }

        $twoCharOperator = $fragment[0] . $fragment[1];

        if ($twoCharOperator === '>=') {
            $amount = substr($fragment, 2);

            if (is_numeric($amount) && $transaction->getAmount() >= (float)$amount) {
                return $res;
            }
        }

        if ($twoCharOperator === '<=') {
            $amount = substr($fragment, 2);

            if (is_numeric($amount) && $transaction->getAmount() <= (float)$amount) {
                return $res;
            }
        }

        if ($fragment[0] === '<' && $transaction->getAmount() < (float)substr($fragment, 1)) {
            return $res;
        }

        return !$res;
    }
}

<?php

namespace App\Services;

use App\Http\Resources\TransactionResource;
use App\Models\Neo\Tag;
use App\Models\Neo\Transaction;
use GraphAware\Neo4j\OGM\EntityManager;

class TransactionService
{
    public const DEFAULT_TRANSACTION_SEARCH_ORDER = 'DESC';

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
    private const DATE_SEARCH_FORMAT = 'D l F M Y-m-d a';

    private const TRANSACTION_SEARCH_DELIMITER = " ";
    private const TRANSACTION_SEARCH_NEGATION_SYMBOL = '!';
    private const TRANSACTION_SEARCH_TAG_SYMBOL = '#';
    private const TRANSACTION_PIE_CHART_UNTAGGED = 'untagged';
    private const TRANSACTION_LINE_CHART_DATE_FORMAT = 'Y-m-d';

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
     * @param string $order
     *
     * @return array|mixed
     */
    public function getAllTransactions(int $userId, string $order = self::DEFAULT_TRANSACTION_SEARCH_ORDER)
    {
        $query = "
            MATCH (:User {sqlId: {sqlId}})-[:HAS_TRANSACTION]->(t:Transaction)
            RETURN DISTINCT t
            ORDER BY t.timestamp $order
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
     * @param array $transactions
     *
     * @return array
     */
    public function getMetaDataFromGivenTransactions(array $transactions): array
    {
        $totalAmount = 0;
        /** @var Transaction $singleMinExpense */
        $singleMinExpense = null;
        /** @var Transaction $singleMaxExpense */
        $singleMaxExpense = null;

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $transactionAmount = $transaction->getAmount();

            $totalAmount += $transactionAmount;

            if ($singleMinExpense === null || $transactionAmount < $singleMinExpense->getAmount()) {
                $singleMinExpense = $transaction;
            }

            if ($singleMaxExpense === null || $transactionAmount > $singleMinExpense->getAmount()) {
                $singleMaxExpense = $transaction;
            }
        }

        // TODO: These generate function are super inefficient, if time allowed, convert them to one single foreach

        return [
            'totalAmount' => number_format($totalAmount, 2, '.', ''),
            'minExpense'  => $singleMinExpense ? TransactionResource::make($singleMinExpense) : null,
            'maxExpense'  => $singleMaxExpense ? TransactionResource::make($singleMaxExpense) : null,
            'pieChart'    => $this->generatePieChart($transactions),
            'lineChart'   => $this->generateLineChart($transactions),
            'tagCloud'    => $this->generateTagCloud($transactions),
        ];
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
        if ($fragment === '') {
            return true;
        }

        $fragment = strtolower($fragment);

        $res = true;

        /* Handle negation */
        if ($fragment[0] == self::TRANSACTION_SEARCH_NEGATION_SYMBOL) {
            $res = false;
            $fragment = substr($fragment, 1);
        }

        if ($fragment === '') {
            return $res;
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

            $fragTime = strtotime($amount);

            if ($fragTime !== false && $transaction->getTimestamp() > $fragTime) {
                return $res;
            }
        }

        if ($fragment[0] === '<') {
            $amount = substr($fragment, 1);

            if (is_numeric($amount) && $transaction->getAmount() < (float)$amount) {
                return $res;
            }

            $fragTime = strtotime($amount);

            if ($fragTime !== false && $transaction->getTimestamp() < $fragTime) {
                return $res;
            }
        }

        /* Check if description contains the word */
        if (strpos(strtolower($transaction->getDescription()), $fragment) !== false) {
            return $res;
        }

        /* Check if date cointains the word */
        $dateFilterString = strtolower(date(self::DATE_SEARCH_FORMAT, $transaction->getTimestamp()));

        if (strpos($dateFilterString, $fragment) !== false) {
            return $res;
        }

        /* Check tags */
        if ($fragment[0] === self::TRANSACTION_SEARCH_TAG_SYMBOL) {
            $fragment = substr($fragment, 1);

            $tagString = '';

            /** @var Tag $tag */
            foreach ($transaction->getTags() as $tag) {
                $tagString .= $tag->getName();
            }

            if ($fragment === '') {
                return $fragment === $tagString ? !$res : $res;
            }

            if (strpos(strtolower($tagString), $fragment) !== false) {
                return $res;
            }

            return !$res;
        }

        /* Check two chars comparison operator */
        if (strlen($fragment) <= 1) {
            return !$res;
        }

        $twoCharOperator = $fragment[0] . $fragment[1];

        if ($twoCharOperator === '>=') {
            $amount = substr($fragment, 2);

            if (is_numeric($amount) && $transaction->getAmount() >= (float)$amount) {
                return $res;
            }

            $fragTime = strtotime($amount);

            if ($fragTime !== false && $transaction->getTimestamp() >= $fragTime) {
                return $res;
            }
        }

        if ($twoCharOperator === '<=') {
            $amount = substr($fragment, 2);

            if (is_numeric($amount) && $transaction->getAmount() <= (float)$amount) {
                return $res;
            }

            $fragTime = strtotime($amount);

            if ($fragTime !== false && $transaction->getTimestamp() <= $fragTime) {
                return $res;
            }
        }

        return !$res;
    }


    /**
     * @param array $transactions
     *
     * @return array
     */
    private function generateLineChart(array $transactions): array
    {
        $results = [];

        $xs = [];

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $x = date(self::TRANSACTION_LINE_CHART_DATE_FORMAT, $transaction->getTimestamp());

            if (isset($xs[$x])) {
                $xs[$x] += $transaction->getAmount();
            } else {
                $xs[$x] = $transaction->getAmount();
            }
        }

        foreach ($xs as $x => $y) {
            $results[] = [
                'x' => $x,
                'y' => $y,
            ];
        }

        return $results;
    }

    /**
     * @param array $transactions
     *
     * @return array
     */
    private function generatePieChart(array $transactions): array
    {
        $results = [self::TRANSACTION_PIE_CHART_UNTAGGED => 0];

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $amount = $transaction->getAmount();

            $tagCount = 0;

            /** @var Tag $tag */
            foreach ($transaction->getTags() as $tag) {
                $tagName = $tag->getName();

                if (isset($results[$tagName])) {
                    $results[$tagName] += $amount;
                } else {
                    $results[$tagName] = $amount;
                }

                $tagCount++;
            }

            if ($tagCount === 0) {
                $results[self::TRANSACTION_PIE_CHART_UNTAGGED] += $amount;
            }
        }

        $chart = [];

        foreach ($results as $item => $count) {
            $chart[] = [
                'item'  => $item,
                'count' => $count,
            ];
        }

        return $chart;
    }

    /**
     * @param array $transactions
     *
     * @return array
     */
    private function generateTagCloud(array $transactions): array
    {
        $tags = [];

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            /** @var Tag $tag */
            foreach ($transaction->getTags() as $tag) {
                $tagName = $tag->getName();

                if (isset($tags[$tagName])) {
                    $tags[$tagName] += 1;
                } else {
                    $tags[$tagName] = 1;
                }
            }
        }

        $results = [];

        foreach ($tags as $name => $value) {
            $results[] = [
                'name'  => $name,
                'value' => $value,
            ];
        }

        return $results;
    }
}

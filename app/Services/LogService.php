<?php

namespace App\Services;

use App\Models\Neo\Log;
use App\Models\Neo\User;
use GraphAware\Neo4j\OGM\EntityManager;

/**
 * Class LogService
 * @package App\Services
 */
class LogService
{
    public const DEFAULT_LOG_LIMITS = 100;

    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * LogService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function showAllLogs(int $limit = self::DEFAULT_LOG_LIMITS, int $offset = 0): array
    {
        $query = "
            MATCH (log:Log)<-[:LOGGED]-(from:User)
            OPTIONAL MATCH (log)-[:ASSOCIATED_WITH]->(to:User)
            RETURN from, log, to
            ORDER BY log.timestamp DESC
            SKIP {offset} LIMIT {limit}
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('limit', $limit)
            ->setParameter('offset', $offset)
            ->addEntityMapping('from', User::class)
            ->addEntityMapping('log', Log::class)
            ->getResult();
    }

    /**
     * @param int $id
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function showGivenUserLogs(int $id, int $limit = self::DEFAULT_LOG_LIMITS, int $offset = 0): array
    {
        $query = "
            MATCH (from:User {sqlId: {id}})
            MATCH (from)-[:LOGGED]->(log:Log)
            OPTIONAL MATCH (log)-[:ASSOCIATED_WITH]->(to:User)
            RETURN from, log, to
            ORDER BY log.timestamp DESC
            SKIP {offset} LIMIT {limit}
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->setParameter('limit', $limit)
            ->setParameter('offset', $offset)
            ->addEntityMapping('from', User::class)
            ->addEntityMapping('log', Log::class)
            ->getResult();
    }
}

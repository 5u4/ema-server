<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\EntityManager;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * App\Models\Neo\Log
 *
 * @OGM\Node (label="Log")
 */
class Log
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $activity;

    /**
     * @OGM\Property(type="int")
     * @var int
     */
    protected $timestamp;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getActivity(): string
    {
        return $this->activity;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param string $activity
     */
    public function setActivity(string $activity): void
    {
        $this->activity = $activity;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @param string $activity
     * @param int|null $targetGraphId
     * @param bool $isTargetSqlId
     *
     * @return Log
     */
    public static function activity(string $activity, int $targetGraphId = null, bool $isTargetSqlId = false): Log
    {
        $id = Auth::id();

        if ($id === null) {
            throw new UnauthorizedHttpException('You are not logged in');
        }

        /** @var EntityManager $entityManager */
        $entityManager = app()->make(EntityManager::class);

        $query = "
            MATCH (u:User {sqlId: {id}})
            CREATE (l:Log {activity: {activity}, timestamp: {timestamp}})<-[:LOGGED]-(u)
        ";

        if ($targetGraphId) {
            $query .= "
                WITH l
                MATCH (t)
            ";

            $query .= $isTargetSqlId ? " WHERE t.sqlId = $targetGraphId " : " WHERE ID(t) = $targetGraphId ";

            $query .= "
                CREATE (l)-[:ASSOCIATED_WITH]->(t)
            ";
        }

        $query .= "
            RETURN l
        ";

        return $entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->setParameter('activity', $activity)
            ->setParameter('timestamp', time())
            ->addEntityMapping('l', Log::class)
            ->getOneResult();
    }
}

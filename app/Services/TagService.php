<?php

namespace App\Services;

use App\Models\Neo\Tag;
use GraphAware\Neo4j\OGM\EntityManager;

/**
 * Class TagService
 * @package App\Services
 */
class TagService
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * TagService constructor.
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
    public function getAllTagsBelongsToUser(int $userId)
    {
        $query = "
            MATCH (u:User {sqlId: {id}})-[:HAS_TAG]->(t)
            RETURN DISTINCT t
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $userId)
            ->addEntityMapping('t', Tag::class)
            ->getResult();
    }

    /**
     * @param int $userId
     * @param int $tagId
     *
     * @return mixed
     */
    public function getUserTag(int $userId, int $tagId)
    {
        $query = "
            MATCH (u:User {sqlId: {uid}})
            MATCH (u)-[:HAS_TAG]->(t:Tag) WHERE ID(t) = {id}
            RETURN t
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('uid', $userId)
            ->setParameter('id', $tagId)
            ->addEntityMapping('t', Tag::class)
            ->getOneResult();
    }

    /**
     * @param int $userId
     * @param string $name
     *
     * @return mixed
     */
    public function createTag(int $userId, string $name)
    {
        $query = "
            MERGE (u:User {sqlId: {id}})
            MERGE (u)-[:HAS_TAG]->(t:Tag {name: {name}})
            RETURN t
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $userId)
            ->setParameter('name', $name)
            ->addEntityMapping('t', Tag::class)
            ->getOneResult();
    }

    /**
     * @param int $userId
     * @param int $tagId
     * @param string $name
     *
     * @return mixed
     */
    public function updateTag(int $userId, int $tagId, string $name)
    {
        $query = "
            MATCH (u:User {sqlId: {uid}})
            MATCH (u)-[:HAS_TAG]->(t:Tag) WHERE ID(t) = {id}
            SET t.name = {name}
            RETURN t
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('uid', $userId)
            ->setParameter('id', $tagId)
            ->setParameter('name', $name)
            ->addEntityMapping('t', Tag::class)
            ->getOneResult();
    }

    /**
     * @param int $userId
     * @param int $tagId
     *
     * @throws \Exception
     */
    public function detachDeleteTag(int $userId, int $tagId)
    {
        $query = "
            MATCH (u:User {sqlId: {uid}})
            MATCH (u)-[:HAS_TAG]->(t:Tag) WHERE ID(t) = {id}
            DETACH DELETE t
        ";

        $this->entityManager->createQuery($query)
            ->setParameter('uid', $userId)
            ->setParameter('id', $tagId)
            ->execute();
    }
}

<?php

namespace App\Services;

use App\Models\Neo\Tag;
use GraphAware\Neo4j\OGM\EntityManager;

/**
 * Class MovieService
 * @package App\Services
 */
class MovieService
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * MovieService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $movieId
     *
     * @return array|mixed
     */
    public function getAllMoviesBelongsToUser(int $userId)
    {
        $query = "";
        return $this->entityManager->createQuery($query)
            ->setParameter('id', $userId)
            ->addEntityMapping('t', Tag::class)
            ->getResult();
    }

    /**
     * @param int $userId
     * @param string $name
     *
     * @return mixed
     */
    public function createMovie(int $userId, string $name)
    {
        $query = "
            MERGE (u:User {sqlId: {id}})
            MERGE (u)-[:WATCH_MOVIE]->(m:Movie {name: {name}})
            RETURN m
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $userId)
            ->setParameter('name', $name)
            ->addEntityMapping('m', Movie::class)
            ->getOneResult();
    }

    /**
     * @param int $userId
     * @param int $movieId
     * @param string $name
     *
     * @return mixed
     */
    public function updateMovie(int $userId, int $movieId, string $name)
    {
        $query = "
            MATCH (u:User {sqlId: {uid}})
            MATCH (u)-[:WATCH_MOVIE]->(m:Movie) WHERE ID(m) = {id}
            SET m.name = {name}
            RETURN m
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('uid', $userId)
            ->setParameter('id', $movieId)
            ->setParameter('name', $name)
            ->addEntityMapping('m', Movie::class)
            ->getOneResult();
    }

    /**
     * @param int $userId
     * @param int $movieId
     *
     * @throws \Exception
     */
    public function detachDeleteMovie(int $userId, int $movieId)
    {
        $query = "
            MATCH (u:User {sqlId: {uid}})
            MATCH (u)-[:WATCH_MOVIE]->(m:Movie) WHERE ID(m) = {id}
            DETACH DELETE m
        ";

        $this->entityManager->createQuery($query)
            ->setParameter('uid', $userId)
            ->setParameter('id', $movieId)
            ->execute();
    }




}
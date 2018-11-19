<?php

namespace App\Services;

use App\Models\Neo\Movie;
use App\Models\Neo\WatchMovie;
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
     * @param int $userId
     *
     * @return array|mixed
     */
    public function getAllMoviesBelongsToUser(int $userId)
    {
        $query = "
            MATCH (u:User {sqlId: {id}})-[:WATCH_MOVIE]->(m)
            RETURN DISTINCT m
        ";
        return $this->entityManager->createQuery($query)
            ->setParameter('id', $userId)
            ->addEntityMapping('m', Movie::class)
            ->getResult();
    }

    /**
     * @param int $userId
     * @param int $movieId
     *
     * @return mixed
     */
    public function getUserMovie(int $userId, int $movieId)
    {
        $query = "
            MATCH (u:User {sqlId: {uid}})
            MATCH (u)-[:WATCH_MOVIE]->(m:Movie) WHERE m.movieId = {id}
            RETURN m
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('uid', $userId)
            ->setParameter('id', $movieId)
            ->addEntityMapping('m', Movie::class)
            ->getOneResult();
    }

    /**
     * @param int $userId
     * @param string $name
     * @param int $movieId
     * @param string $posterURL
     *
     * @return mixed
     */
    public function createMovie(int $userId, string $name, int $movieId, string $posterURL)
    {
        $query = "
            MERGE (u:User {sqlId: {id}})
            MERGE (u)-[:WATCH_MOVIE]->(m:Movie {name: {name}, movieId: {movieId}, posterURL: {posterURL}})
            RETURN m
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $userId)
            ->setParameter('name', $name)
            ->setParameter('movieId', $movieId)
            ->setParameter('posterURL', $posterURL)
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
            MATCH (u)-[w:WATCH_MOVIE]->(m:Movie) WHERE m.movieId = {id}
            DELETE w
        ";

        $this->entityManager->createQuery($query)
            ->setParameter('uid', $userId)
            ->setParameter('id', $movieId)
            ->execute();
    }
}

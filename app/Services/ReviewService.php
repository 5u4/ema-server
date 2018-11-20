<?php

namespace App\Services;

use App\Models\Neo\Review;
use GraphAware\Neo4j\OGM\EntityManager;

/**
 * Class ReviewService
 * @package App\Services
 */
class ReviewService
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * ReviewService constructor.
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
    public function getAllReviewBelongsToMovie(int $movieId)
    {
        $query = "
            MATCH (m:Movie {movieId: {mid}})-[:HAS_REVIEW]->(r)
            RETURN DISTINCT r
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('mid', $movieId)
            ->addEntityMapping('r', Review::class)
            ->getResult();
    }

    /**
     * @param int $reviewId
     *
     * @return mixed
     */
    public function getMovieReview(int $reviewId)
    {
        $query = "
            MATCH (m:Movie)-[:HAS_REVIEW]->(r:Review) WHERE ID(r) = {id}
            RETURN r
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $reviewId)
            ->addEntityMapping('r', Review::class)
            ->getOneResult();
    }

    /**
     * @param int $userId
     * @param string $reviewTitle
     * @param string $reviewContent
     * @param int $movieId
     *
     * @return mixed
     */
    public function createReview(int $userId, string $reviewTitle, string $reviewContent, int $movieId)
    {
        $query = "
            MERGE (m:Movie {movieId: {movieId}})
            MERGE (m)-[:HAS_REVIEW]->(r:Review {userId: {userId}, reviewTitle: {reviewTitle}, reviewContent: {reviewContent}})
            RETURN r
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('userId', $userId)
            ->setParameter('reviewTitle', $reviewTitle)
            ->setParameter('reviewContent', $reviewContent)
            ->setParameter('movieId', $movieId)
            ->addEntityMapping('r', Review::class)
            ->getOneResult();
    }

    /**
     * @param int $reviewId
     *
     * @throws \Exception
     */
    public function detachDeleteReview(int $reviewId)
    {
        $query = "
            MATCH (m:Movie)-[h:HAS_REVIEW]->(r) WHERE ID(r) = {id}
            DETACH DELETE h,r
        ";

        $this->entityManager->createQuery($query)
            ->setParameter('id', $reviewId)
            ->execute();
    }
}

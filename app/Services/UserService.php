<?php

namespace App\Services;
use App\Models\Sql\User;
use App\Models\Neo\User as NeoUser;
use GraphAware\Neo4j\OGM\EntityManager;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{
    private const FRIEND_SUGGESTION_LIMIT = 5;

    private $entityManager;

    /**
     * UserService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Insert an user into sql database (Without validation)
     *
     * @param string $username
     * @param string $email
     * @param string $password
     *
     * @return User
     */
    public function createUserInSql(string $username, string $email, string $password): User
    {
        return User::create([
            'username'   => $username,
            'email'      => $email,
            'password'   => bcrypt($password),
        ]);
    }

    /**
     * @param int $sqlId
     *
     * @return NeoUser
     */
    public function createUserInNeo(int $sqlId): NeoUser
    {
        $query = "
            MERGE (u:User {sqlId: {id}})
            RETURN u
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $sqlId)
            ->addEntityMapping('u', NeoUser::class)
            ->getOneResult();
    }

    /**
     * @param $userId
     * @return array|mixed|null
     * @throws \Exception
     */
    public function getUserInfo($userId)
    {
        $query = "
            MATCH(u:User {sqlId: {sqlId}})-[:HAS_TRANSACTION]->(t:Transaction)
            RETURN t
        ";

        $user = null;

        $user = $this->entityManager->createQuery($query)
            ->setParameter('sqlId', $userId)
            ->addEntityMapping('u', User::class)
            ->addEntityMapping('t', Transaction::class)
            ->execute();

        return $user;

    }

    /**
     * @param string $input
     * @param bool $withTrashed
     *
     * @return \Illuminate\Support\Collection
     */
    public function searchUser(string $input, bool $withTrashed = false)
    {
        $searchString = '%' . $input . '%';

        $users = User::where('username', 'like', $searchString)
            ->orWhere('email', 'like', $searchString);

        if ($withTrashed) {
            $users = $users->withTrashed();
        }

        return $users->get();
    }

    /**
     * @param int $id
     * @param int $followingId
     *
     * @return mixed
     */
    public function followUser(int $id, int $followingId)
    {
        $query = "
            MATCH (u:User {sqlId: {id}})
            MATCH (following:User {sqlId: {fid}})
            MERGE (u)-[:FOLLOW]->(following)
            RETURN following
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->setParameter('fid', $followingId)
            ->addEntityMapping('following', NeoUser::class)
            ->getOneResult();
    }

    /**
     * @param int $id
     * @param int $unFollowingId
     *
     * @return NeoUser
     */
    public function unFollowUser(int $id, int $unFollowingId): NeoUser
    {
        $query = "
            MATCH (:User {sqlId: {id}})-[r:FOLLOW]->(u:User {sqlId: {fid}})
            DELETE r
            RETURN u
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->setParameter('fid', $unFollowingId)
            ->addEntityMapping('u', NeoUser::class)
            ->getOneResult();
    }

    /**
     * @param int $id
     *
     * @return NeoUser
     */
    public function getNeoUser(int $id): NeoUser
    {
        $query = "
            MATCH (u:User {sqlId: {id}})
            RETURN u
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->addEntityMapping('u', NeoUser::class)
            ->getOneResult();
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getUserFollowersInSql(int $id): array
    {
        /** @var NeoUser $user */
        $user = $this->getNeoUser($id);

        $followers = [];

        foreach ($user->getFollowers() as $follower) {
            $followers[] = User::find($follower->getSqlId());
        }

        return $followers;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getUserFollowingsInSql(int $id): array
    {
        /** @var NeoUser $user */
        $user = $this->getNeoUser($id);

        $followings = [];

        foreach ($user->getFollowings() as $following) {
            $followings[] = User::find($following->getSqlId());
        }

        return $followings;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getCommonFriends(int $id): array
    {
        $query = "
            MATCH (u:User {sqlId: {id}})-[:FOLLOW]->(friends:User)-[:FOLLOW]->(u)
            MATCH (friends)-[:FOLLOW]->(commonfriends:User)-[:FOLLOW]->(friends)
            WHERE NOT (u)-[:FOLLOW]->(commonfriends)
            AND NOT commonfriends.sqlId = {id}
            RETURN count(commonfriends) AS occurrence, commonfriends.sqlId AS commonFriendId
            ORDER BY occurrence DESC LIMIT {limit}
        ";

        $result = $this->entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->setParameter('limit', self::FRIEND_SUGGESTION_LIMIT)
            ->getResult();

        $friends = [];

        foreach ($result as $friend) {
            $friends[] = User::find($friend['commonFriendId']);
        }

        return $friends;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getFriends(int $id): array
    {

        $query = "
           MATCH (u:User {sqlId: {id}})-[:FOLLOW]->(friends:User)-[:FOLLOW]->(u)
            WHERE NOT friends.sqlId = {id}
            RETURN friends.sqlId AS friendId
        ";

        $result = $this->entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->getResult();

        $friends = [];

        foreach ($result as $friend) {
            $friends[] = User::find($friend['friendId']);
        }

        return $friends;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function isFollowing(int $id, int $isFollowingId)
    {
        $query = '
        MATCH  (a:User {sqlId: {id}}), (b:User {sqlId: {fid}}) 
        RETURN EXISTS( (a)-[:FOLLOW]->(b) ) AS isFollowing
        ';
        $result = $this->entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->setParameter('fid', $isFollowingId)
            ->getOneResult();

        return $result;

    }
}

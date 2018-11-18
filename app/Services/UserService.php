<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
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
 * Insert an user into neo database (Without validation)
 *
 * @param $sqlId
 * @return NeoUser
 * @throws \Exception
 */
    public function createUserInNeo($sqlId)
    {
        $user = new NeoUser();

        $user->setSqlId($sqlId);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
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

    public function searchUser($input){


        $users = DB::table('users')
            ->where('username', $input)
            ->orWhere('id', $input)
            ->orWhere('email', $input)
            ->get();
        return $users;
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
     * @return array|mixed
     * @throws \Exception
     */
    public function unFollowUser(int $id, int $unFollowingId)
    {
        $query = "
            MATCH (:User {sqlId: {id}})-[r:FOLLOW]->(:User {sqlId: {fid}})
            DELETE r
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $id)
            ->setParameter('fid', $unFollowingId)
            ->execute();
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function getNeoUser(int $id)
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
}

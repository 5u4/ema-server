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

}

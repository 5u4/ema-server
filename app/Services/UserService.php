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
}

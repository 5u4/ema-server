<?php

namespace App\Services;

use App\Models\Sql\User;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{
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
}

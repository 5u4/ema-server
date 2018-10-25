<?php

namespace App\Http\Services;

use App\Models\Neo\User;
use GraphAware\Neo4j\OGM\EntityManager;

class NeoUserService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createNeoUser($name, $sqlId)
    {
        $user = new User();

        $user->setMysqlId($sqlId);
        $user->setName($name);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}

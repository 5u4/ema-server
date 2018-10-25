<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * @OGM\Node(label="User")
 * Class User
 * @package App\Models\Neo
 */
class User
{
    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="int")
     * @var int
     */
    protected $sqlId;

    /**
     * User constructor.
     */
    public function __construct()
    {

    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSqlId(): int
    {
        return $this->sqlId;
    }

    /**
     * @param int $sqlId
     */
    public function setSqlId($sqlId)
    {
        $this->sqlId = $sqlId;
    }
}

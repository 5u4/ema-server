<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * App\Models\Neo\Restaurant
 *
 * @OGM\Node (label="Restaurant")
 */
class Restaurant
{
    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $rest_id;

    /**
     * @OGM\GraphId()
     * @var int
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * Restaurant constructor.
     */
    public function __construct()
    {
        $this->rest_id = new Collection();
    }

    /**
     * @return string
     */
    public function getRestId(): string
    {
        return $this->rest_id;
    }

    /**
     * @param string $rest_id
     */
    public function setRestId(string $rest_id): void
    {
        $this->rest_id = $rest_id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }


}

<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * App\Models\Neo\Tag
 *
 * @OGM\Node (label="Tag")
 */
class Tag
{
    public const TAG_COLORS = [
        'magenta', 'red', 'volcano', 'orange', 'gold', 'lime', 'green', 'cyan', 'blue', 'geekblue', 'purple',
    ];

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
     * @OGM\Relationship(type="HAS_TAG", direction="INCOMING", collection=true, mappedBy="tags", targetEntity="User")
     * @var User[]|Collection
     */
    protected $users;

    /**
     * @OGM\Relationship(type="TAGGED_AS", direction="INCOMING", collection=true, mappedBy="tags", targetEntity="Transaction")
     * @var Transaction[]|Collection
     */
    protected $transactions;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->users = new Collection();
        $this->transactions = new Collection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        $index = ord($this->name[0]) % count(self::TAG_COLORS);

        return self::TAG_COLORS[$index];
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return User[]|Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}

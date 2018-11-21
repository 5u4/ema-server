<?php

namespace App\Models\Neo;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;
use phpDocumentor\Reflection\Types\Mixed_;

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
     * @OGM\Property(type="string")
     * @var string
     */
    protected $image_url;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $phone;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $address;

    /**
     * @OGM\Property(type="string")
     * @var string
     */
    protected $city;

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * @param mixed $image_url
     */
    public function setImageUrl($image_url): void
    {
        $this->image_url = $image_url;
    }

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

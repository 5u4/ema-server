<?php

namespace App\Services;
use App\Models\Neo\Restaurant;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use GraphAware\Neo4j\OGM\EntityManager;
use PhpParser\Error;


class DiningService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param String $input
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search(string $location, string $price,string $categories,string $sortby,string $attributes,string $open_now)
    {

        $url = 'https://api.yelp.com/v3/businesses/search?term=restaurants&limit=50' ;
        if($location!==""){
            $url .= "&location=" . $location;
        }
        if($price!=="") {
            $url .= "&price=" . $price;
        }
        if($categories !== "") {
            $url .= "&categories=" . $categories;
        }
        if($sortby !== "") {
            $url .= "&sort_by=" . $sortby;
        }
        if($attributes !== "") {
            $url .= "&attributes=" . $attributes;
        }
        if($open_now !== "") {
            $url .= "&open_now=" . $open_now;
        }
        $requestContent = [
            'headers' => [
                'Authorization'=> "Bearer EXCKgny_5NI0-DuoD-vpEGcsowVY15hUCH60XlgrzSQaePnXN-ghbw0Cv8spDYmmdqcrFEDpKXKVU6oZSb6mxPWtDZqZbBrTD-hBhhTbKz0JFM-jM2vGwXsLi43WW3Yx"
            ]
        ];

        try {
            $client = new GuzzleHttpClient();

            $apiRequest = $client->request('GET', $url, $requestContent);

            $response = json_decode($apiRequest->getBody()->getContents(), true);

            return $response["businesses"];

        } catch (RequestException $re) {
            // For handling exception.
            $response = $re->getResponse();
            return $response;
        }

    }

    public function getUserRestaurants(int $userId)
    {
        $query = "
            MATCH (u:User {sqlId: {id}})-[:favs]->(r:Restaurant)
            RETURN DISTINCT r
        ";
        return $this->entityManager->createQuery($query)
            ->setParameter('id', $userId)
            ->addEntityMapping('r', Restaurant::class)
            ->getResult();
    }

    public function createRestaurant(int $userId, string $name, string $rest_id)
    {
        $query = "
            MERGE (u:User {sqlId: {id}})
            MERGE (r:Restaurant {rest_id: {rest_id}})
            MERGE (u)-[:favs]->(r)
            SET r.name = {name}
            RETURN r
        ";

        return $this->entityManager->createQuery($query)
            ->setParameter('id', $userId)
            ->setParameter('name', $name)
            ->setParameter('rest_id', $rest_id)
            ->addEntityMapping('r', Restaurant::class)
            ->getOneResult();
    }

}

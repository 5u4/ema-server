<?php

namespace App\Services;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use GraphAware\Neo4j\OGM\EntityManager;



class DiningService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getRestaurantList(String $input)
    {
        $url = 'https://api.yelp.com/v3/businesses/search?term=restaurants&location='.$input.'&limit=50';
        $requestContent = [
            'headers' => [
                'Authorization'=> "Bearer EXCKgny_5NI0-DuoD-vpEGcsowVY15hUCH60XlgrzSQaePnXN-ghbw0Cv8spDYmmdqcrFEDpKXKVU6oZSb6mxPWtDZqZbBrTD-hBhhTbKz0JFM-jM2vGwXsLi43WW3Yx"
            ]
        ];

        try {
            $client = new GuzzleHttpClient();

            $apiRequest = $client->request('GET', $url, $requestContent);

            $response = json_decode($apiRequest->getBody()->getContents(), true);
            $response=$response["businesses"];

           return $response;

        } catch (RequestException $re) {
            // For handling exception.
        }

    }

    public function create($userId, $amount, $description)
    {
        $query = "
            MERGE (u:User {sqlId: {userId}})
            CREATE (t:Transaction {amount: {amount}, description: {description}})
            CREATE (u)-[:HAS_TRANSACTION]->(t)
            RETURN t
        ";

        $transaction = null;

        $transaction = $this->entityManager->createQuery($query)
            ->setParameter('userId', $userId)
            ->setParameter('amount', $amount)
            ->setParameter('description', $description)
            ->addEntityMapping('u', User::class)
            ->addEntityMapping('t', Transaction::class)
            ->getOneResult();

        return $transaction;

    }
}

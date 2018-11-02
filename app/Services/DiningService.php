<?php

namespace App\Services;
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

            return $response["businesses"];

        } catch (RequestException $re) {
            // For handling exception.
            $response = $re->getResponse();
            return $response;
        }

    }

}

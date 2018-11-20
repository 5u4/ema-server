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
        $oInput = json_decode($input);
        $url = 'https://api.yelp.com/v3/businesses/search?term=restaurants&location=' . $oInput->location;
        if(array_key_exists('price', $oInput) && $oInput->price !== "") {
            $url .= "&price=" . $oInput->price;
        }
        if(array_key_exists('categories', $oInput) && $oInput->categories !== "") {
            $url .= "&categories=" . $oInput->categories;
        }
        if(array_key_exists('sort_by', $oInput) && $oInput->sort_by !== "") {
            $url .= "&sort_by=" . $oInput->sort_by;
        }
        if(array_key_exists('attributes', $oInput) && $oInput->attributes !== "") {
            $url .= "&attributes=" . $oInput->attributes;
        }
        if(array_key_exists('open_now', $oInput) && $oInput->open_now !== "") {
            $url .= "&open_now=" . $oInput->open_now;
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

}

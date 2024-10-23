<?php

namespace App\Services\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;


/**
 * this service is very simplfied to handle only retreiving products from the mockApi
 */
class HttpClientService
{
    /** @var Client */
    protected $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws GuzzleException
     * @throws Throwable
     */
    public function getRequest(
        string $endpoint,
        array $params = [],
        array $headers = []
    ) {
        $requestBody = [
            'headers' => $headers,
            'query' => $params,
            'http_errors' => true,
        ];

        $response = $this->httpClient->get($endpoint, $requestBody);

        return json_decode($response->getBody(), true);
    }
}

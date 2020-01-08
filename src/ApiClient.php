<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;

final class ApiClient
{
    private const SPORTYSKY_API_URL = 'https://api.sportysky.com';

    /**
     * Sportysky Guzzle client
     *
     * @var Client
     */
    private $http;

    /**
     * @param string $token JWT token provided by Authenticator
     * @param HandlerStack|null $handlerStack useful to mock API calls for tests
     */
    public function __construct(string $token, HandlerStack $handlerStack = null)
    {
        $this->http = new Client([
            'base_uri' => getenv('SPORTYSKY_API_URL') ?: self::SPORTYSKY_API_URL,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ],
            'handler' => $handlerStack
        ]);
    }

    public function getForecast(string $mapView): array
    {
        try {
            $reponse = $this->http->get('/forecast/customers/me/theme', [
                'query' => [
                    'groups[]' => 'forecast',
                    'mapView' => $mapView
                ]
            ]);

            return json_decode($reponse->getBody()->getContents(), true);
        } catch (ServerException $e) {
            throw new Exception($e->getResponse()->getBody()->getContents());
        }
    }
}

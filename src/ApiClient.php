<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use Sportrizer\Sportysky\Exception\BadRequestException;

final class ApiClient
{
    private const SPORTYSKY_API_URL = 'https://api.sportysky.com';
    private const BAD_REQUEST_MESSAGE = 'Bad request';

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
        } catch (ClientException | ServerException $e) {
            throw new BadRequestException(self::BAD_REQUEST_MESSAGE);
        }
    }
}

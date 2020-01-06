<?php

namespace Sportrizer\Sportysky;

use GuzzleHttp\Client;

final class ApiClient
{
    private const SPORTYSKY_API_URL = 'https://api.sportysky.com';

    /**
     * JWT token provided by Authenticator
     *
     * @var string
     */
    private $token;

    /**
     * Sportysky Guzzle client
     *
     * @var Client
     */
    private $http;

    public function __construct(string $token)
    {
        $this->token = $token;
        $this->http = new Client([
            'base_uri' => getenv('SPORTYSKY_API_URL') ?? self::SPORTYSKY_API_URL,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);
    }

    public function getDepartmentForecast(string $isoCode): array
    {
        $reponse = $this->http->get('/forecast/customers/me/theme', [
            'query' => [
                'groups' => ['forecast'],
                'spots.department.isoCode' => $isoCode
            ]
        ]);

        return json_decode($reponse->getBody()->getContents(), true);
    }
}

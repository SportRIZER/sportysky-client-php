<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;

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
     */
    public function __construct(string $token)
    {
        $this->http = new Client([
            'base_uri' => getenv('SPORTYSKY_API_URL') ?: self::SPORTYSKY_API_URL,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        ]);
    }

    public function getDepartmentForecast(string $isoCode): array
    {
        return $this->getForecast($isoCode, 'spots.department.isoCode');
    }

    public function getRegionForecast(string $isoCode): array
    {
        return $this->getForecast($isoCode, 'spots.region.isoCode');
    }

    public function getCountryForecast(string $isoCode): array
    {
        return $this->getForecast($isoCode, 'spots.country.isoCode');
    }

    private function getForecast(string $isoCode, string $filter): array
    {
        try {
            $reponse = $this->http->get('/forecast/customers/me/theme', [
                'query' => [
                    'groups[]' => 'forecast',
                    $filter => $isoCode
                ]
            ]);

            return json_decode($reponse->getBody()->getContents(), true);
        } catch (ServerException $e) {
            throw new Exception($e->getResponse()->getBody()->getContents());
        }
    }
}

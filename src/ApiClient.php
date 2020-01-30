<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
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
     * @param HandlerStack|null $handlerStack useful for caching or mocking API calls in tests
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

    /**
     * Returns a PSR7 response of the forecast API call
     *
     * @param string $mapView
     * @param string $minDate
     * @throws BadRequestException
     * @return Response
     */
    public function getForecastResponse(
        string $minDate,
        string $maxDate = null,
        string $departmentIsoCode = null,
        string $regionIsoCode = null,
        string $countryIsoCode = null,
        string $spotUuid = null
    ): Response {
        try {
            $reponse = $this->http->get('/forecast/customers/me/theme', [
                'query' => [
                    'groups[]' => 'forecast',
                    'minDate' => $minDate,
                    'maxDate' => $maxDate,
                    'departmentIsoCode' => $departmentIsoCode,
                    'regionIsoCode' => $regionIsoCode,
                    'countryIsoCode' => $countryIsoCode,
                    'spotUuid' => $spotUuid
                ]
            ]);

            return $reponse;
        } catch (ClientException | ServerException $e) {
            throw new BadRequestException(self::BAD_REQUEST_MESSAGE);
        }
    }

    /**
     * Returns the API response for a department
     *
     * @param string $departmentIsoCode
     * @param string $minDate
     * @param string $maxDate
     * @return Response
     */
    public function getDepartmentForecastResponse(
        string $departmentIsoCode,
        string $minDate,
        string $maxDate = null
    ): Response {
        return $this->getForecastResponse($minDate, $maxDate, $departmentIsoCode);
    }

    /**
     * Returns the API response for a region
     *
     * @param string $regionIsoCode
     * @param string $minDate
     * @param string $maxDate
     * @return Response
     */
    public function getRegionForecastResponse(
        string $regionIsoCode,
        string $minDate,
        string $maxDate = null
    ): Response {
        return $this->getForecastResponse($minDate, $maxDate, null, $regionIsoCode);
    }

    /**
     * Returns the API response for a country
     *
     * @param string $countryIsoCode
     * @param string $minDate
     * @param string $maxDate
     * @return Response
     */
    public function getCountryForecastResponse(
        string $countryIsoCode,
        string $minDate,
        string $maxDate = null
    ): Response {
        return $this->getForecastResponse($minDate, $maxDate, null, null, $countryIsoCode);
    }

    /**
     * Returns the API response for a single spot
     *
     * @param string $spotUuid
     * @param string $minDate
     * @param string $maxDate
     * @return Response
     */
    public function getSpotForecastResponse(string $spotUuid, string $minDate, string $maxDate): Response
    {
        return $this->getForecastResponse($minDate, $maxDate, null, null, null, $spotUuid);
    }
}

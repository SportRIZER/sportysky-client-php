<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

use DateTimeInterface;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Sportrizer\Sportysky\Exception\BadRequestException;
use Sportrizer\Sportysky\Utils\Geo\Box;
use Sportrizer\Sportysky\Utils\Geo\Point;

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
        DateTimeInterface $minDate,
        DateTimeInterface $maxDate = null,
        string $departmentIsoCode = null,
        string $regionIsoCode = null,
        string $countryIsoCode = null,
        string $spotUuid = null,
        string $spotCode = null
    ): Response {
        $roundMinDate = $minDate->setTime((int) $minDate->format('H'), 0, 0);
        $roundMaxDate = $maxDate ? $maxDate->setTime((int) $maxDate->format('H'), 0, 0) : null;

        return $this->http->get('/forecast/customers/me/theme', [
            'query' => [
                'groups[]' => 'forecast',
                'minDate' => $roundMinDate->format('c'),
                'maxDate' => $roundMaxDate ? $roundMaxDate->format('c') : null,
                'departmentIsoCode' => $departmentIsoCode,
                'regionIsoCode' => $regionIsoCode,
                'countryIsoCode' => $countryIsoCode,
                'spotUuid' => $spotUuid,
                'spotCode' => $spotCode
            ],
            'stream' => true
        ]);
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
        DateTimeInterface $minDate,
        DateTimeInterface $maxDate = null
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
        DateTimeInterface $minDate,
        DateTimeInterface $maxDate = null
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
        DateTimeInterface $minDate,
        DateTimeInterface $maxDate = null
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
    public function getSpotForecastResponse(
        string $spotUuid,
        DateTimeInterface $minDate,
        DateTimeInterface $maxDate
    ): Response {
        return $this->getForecastResponse($minDate, $maxDate, null, null, null, $spotUuid);
    }

    /**
     * Returns the API response for a single spot (by code and country)
     *
     * @param string $code
     * @param string $minDate
     * @param string $maxDate
     * @return Response
     */
    public function getSpotForecastByCodeAndCountryResponse(
        string $spotCode,
        string $countryIsoCode,
        DateTimeInterface $minDate,
        DateTimeInterface $maxDate
    ): Response {
        return $this->getForecastResponse($minDate, $maxDate, null, null, $countryIsoCode, null, $spotCode);
    }

    /**
     * Returns the API response of the spots list
     *
     * @param Point $nearPoint find the nearest spot
     * @param Box $insideBox find all spots inside a box
     * @param int $page page number
     * @return Response
     */
    public function getSpotsResponse(Point $nearPoint = null, Box $insideBox = null, int $page = 1): Response
    {
        return $this->http->get('/forecast/customers/me/theme/spots', [
            'query' => [
                'near_lat' => $nearPoint->lat ?? null,
                'near_lat' => $nearPoint->lng ?? null,
                'inside_p1_lat' => $insideBox->point1->lat ?? null,
                'inside_p1_lng' => $insideBox->point1->lng ?? null,
                'inside_p2_lat' => $insideBox->point2->lat ?? null,
                'inside_p2_lng' => $insideBox->point2->lng ?? null,
                'page' => $page
            ],
            'stream' => true
        ]);
    }
}

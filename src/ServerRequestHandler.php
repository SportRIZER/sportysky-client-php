<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

use DateTime;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

final class ServerRequestHandler
{
    public const DEPARTMENT_ISO_PARAM = 'departmentIsoCode';
    public const REGION_ISO_PARAM = 'regionIsoCode';
    public const COUNTRY_ISO_PARAM = 'countryIsoCode';
    public const SPOT_UUID_PARAM = 'spotUuid';
    public const SPOT_ISO_CODE_PARAM = 'spotCode';

    /**
     * SportySKY API client
     *
     * @var ApiClient
     */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function handle(ServerRequestInterface $serverRequest): Response
    {
        $queryParams = $serverRequest->getQueryParams();
        if (isset($queryParams['minDate'])) {
            $minDate = new DateTime($queryParams['minDate']);
            $maxDate = isset($queryParams['maxDate']) ? new DateTime($queryParams['maxDate']) : null;

            return $this->apiClient->getForecastResponse(
                $minDate,
                $maxDate,
                $queryParams[self::DEPARTMENT_ISO_PARAM] ?? null,
                $queryParams[self::REGION_ISO_PARAM] ?? null,
                $queryParams[self::COUNTRY_ISO_PARAM] ?? null,
                $queryParams[self::SPOT_UUID_PARAM] ?? null,
                $queryParams[self::SPOT_ISO_CODE_PARAM] ?? null
            );
        }

        return new Response(400, [], json_encode((object) ["error" => "Bad request"]));
    }
}

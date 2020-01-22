<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

final class ServerRequestHandler
{
    public const DEPARTMENT_ISO_PARAM = 'departmentIsoCode';
    public const REGION_ISO_PARAM = 'regionIsoCode';

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
            return $this->apiClient->getForecastResponse(
                $queryParams['minDate'],
                $queryParams['maxDate'] ?? null,
                $queryParams[self::DEPARTMENT_ISO_PARAM] ?? null,
                $queryParams[self::REGION_ISO_PARAM] ?? null,
            );
        }

        return new Response(400, [], json_encode((object) ["error" => "Bad request"]));
    }
}

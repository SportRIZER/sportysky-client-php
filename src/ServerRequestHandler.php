<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

final class ServerRequestHandler
{
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
        if (isset($queryParams['mapView']) && isset($queryParams['minDate'])) {
            return $this->apiClient->getForecastResponse(
                $queryParams['mapView'],
                $queryParams['minDate'],
                $queryParams['maxDate'] ?? null
            );
        }

        return new Response(200, [], json_encode(new stdClass()));
    }
}

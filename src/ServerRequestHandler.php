<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

use GuzzleHttp\Psr7\ServerRequest;

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

    public function handle(ServerRequest $serverRequest): array
    {
        $queryParams = $serverRequest->getQueryParams();
        if (isset($queryParams['mapView'])) {
            return $this->apiClient->getForecast($queryParams['mapView']);
        }

        return [];
    }
}

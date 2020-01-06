<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

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

    public function handle(): array
    {
        if ($mapView = filter_input(INPUT_GET, 'mapView', FILTER_SANITIZE_STRING)) {
            return $this->apiClient->getForecast($mapView);
        }

        return [];
    }
}

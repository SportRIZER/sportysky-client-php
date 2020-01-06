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
        if ($isoCode = filter_input(INPUT_GET, 'departmentIsoCode', FILTER_SANITIZE_STRING)) {
            return $this->apiClient->getDepartmentForecast($isoCode);
        }

        return [];
    }
}

<?php

namespace Sportrizer\Sportysky;

use Desarrolla2\Cache\File;
use Psr\SimpleCache\CacheInterface;

final class ApiClient
{
    private const API_AUDIENCE = 'https://api.sportysky.com';
    private const SPORTRIZER_AUTH_URL = 'https://auth.sportrizer.com/oauth/token';

    /**
     * Client ID provided by SportRIZER
     *
     * @var string
     */
    private $clientId;

    /**
     * Client secret provided by SportRIZER
     *
     * @var string
     */
    private $clientSecret;

    /**
     * PSR 16 cache implementation
     *
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $apiAudience;

    /**
     * @var string
     */
    private $sportrizerAuthUrl;

    public function __construct(string $clientId, string $clientSecret, ?CacheInterface $cache = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        if (!$cache) {
            $this->cache = new File();
        }

        $this->apiAudience = getenv('SPORTRIZER_SPORTYSKY_API_AUDIENCE') ?? self::API_AUDIENCE;
        $this->sportrizerAuthUrl = getenv('SPORTRIZER_AUTH_URL') ?? self::SPORTRIZER_AUTH_URL;
    }
}

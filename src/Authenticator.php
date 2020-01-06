<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

use Desarrolla2\Cache\File;
use Psr\SimpleCache\CacheInterface;
use GuzzleHttp\Client;
use Sportrizer\Sportysky\Exception\AuthenticationException;

final class Authenticator
{
    private const API_AUDIENCE = 'https://api.sportysky.com';
    private const SPORTRIZER_AUTH_URL = 'https://auth.sportrizer.com';
    private const AUTH_GRANT_TYPE = 'client_credentials';
    private const TOKEN_CACHE_KEY = 'spotrizer_sportysky_jwt';

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

    /**
     * Guzzle http client
     *
     * @var Client
     */
    private $authApi;

    public function __construct(string $clientId, string $clientSecret, ?CacheInterface $cache = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        if (!$cache) {
            $this->cache = new File(realpath(sys_get_temp_dir()));
        }

        $this->apiAudience = getenv('SPORTRIZER_SPORTYSKY_API_AUDIENCE') ?: self::API_AUDIENCE;
        $this->sportrizerAuthUrl = getenv('SPORTRIZER_AUTH_URL') ?: self::SPORTRIZER_AUTH_URL;
        $this->authApi = new Client(['base_uri' => $this->sportrizerAuthUrl]);
    }

    public function getToken()
    {
        if (!$this->cache->has(self::TOKEN_CACHE_KEY)) {
            return $this->requestNewToken();
        }

        return $this->cache->get(self::TOKEN_CACHE_KEY);
    }

    private function requestNewToken(): string
    {
        $response = $this->authApi->post('/oauth/token', [
            'json' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'audience' => $this->apiAudience,
                'grant_type' => self::AUTH_GRANT_TYPE
            ]
        ]);

        $responseData = json_decode($response->getBody()->getContents(), true);

        if (!isset($responseData['access_token']) || !isset($responseData['expires_in'])) {
            throw new AuthenticationException("Could not authenticate to SportRIZER");
        }

        $this->cache->set(self::TOKEN_CACHE_KEY, $responseData['access_token'], $responseData['expires_in']);

        return $responseData['access_token'];
    }
}

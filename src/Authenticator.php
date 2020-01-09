<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky;

use Desarrolla2\Cache\File;
use Psr\SimpleCache\CacheInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use Sportrizer\Sportysky\Exception\AuthenticationException;

final class Authenticator
{
    private const API_AUDIENCE = 'https://api.sportysky.com';
    private const SPORTRIZER_AUTH_URL = 'https://auth.sportrizer.com';
    private const AUTH_GRANT_TYPE = 'client_credentials';
    private const TOKEN_CACHE_KEY = 'spotrizer_sportysky_jwt';
    private const AUTHENTICATION_EXCEPTION_MESSAGE = 'Could not authenticate to SportRIZER';

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

    /**
     * @param string $clientId SportySKY API client ID provided by SportRIZER
     * @param string $clientSecret SportySKY API client secret provided by SportRIZER
     * @param CacheInterface $cache PSR 16 cache implementation used to save the JWT token (defaults to file system)
     * @param HandlerStack $handlerStack useful to mock API calls for tests
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        CacheInterface $cache = null,
        HandlerStack $handlerStack = null
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->cache = $cache ?: new File(realpath(sys_get_temp_dir()));

        $this->apiAudience = getenv('SPORTRIZER_SPORTYSKY_API_AUDIENCE') ?: self::API_AUDIENCE;
        $this->sportrizerAuthUrl = getenv('SPORTRIZER_AUTH_URL') ?: self::SPORTRIZER_AUTH_URL;
        $this->authApi = new Client([
            'base_uri' => $this->sportrizerAuthUrl,
            'handler' => $handlerStack
        ]);
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
        try {
            $response = $this->authApi->post('/oauth/token', [
                'json' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'audience' => $this->apiAudience,
                    'grant_type' => self::AUTH_GRANT_TYPE
                ]
            ]);
        
            $responseData = json_decode($response->getBody()->getContents(), true);
        } catch (ServerException | ClientException $e) {
            throw new AuthenticationException(self::AUTHENTICATION_EXCEPTION_MESSAGE);
        }

        if (!isset($responseData['access_token']) || !isset($responseData['expires_in'])) {
            throw new AuthenticationException(self::AUTHENTICATION_EXCEPTION_MESSAGE);
        }

        $this->cache->set(self::TOKEN_CACHE_KEY, $responseData['access_token'], $responseData['expires_in']);

        return $responseData['access_token'];
    }
}

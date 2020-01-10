<?php

declare(strict_types=1);

use Desarrolla2\Cache\Predis;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\ServerRequest;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr16CacheStorage;
use Kevinrob\GuzzleCache\Strategy\PublicCacheStrategy;
use Predis\Client;
use Sportrizer\Sportysky\ApiClient;
use Sportrizer\Sportysky\Authenticator;
use Sportrizer\Sportysky\ServerRequestHandler;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

require '../vendor/autoload.php';

$redisCache = new Predis(new Client(getenv('REDIS_URL'))); // tcp://127.0.0.1:6379

$authenticator = new Authenticator(
    getenv('SPORTYSKY_CLIENT_ID'),
    getenv('SPORTYSKY_CLIENT_SECRET'),
    $redisCache
);

$cacheHandler = HandlerStack::create();
$cacheHandler->push(new CacheMiddleware(
    new PublicCacheStrategy(
        new Psr16CacheStorage($redisCache)
    )
));

$apiClient = new ApiClient($authenticator->getToken(), $cacheHandler);
$apiResponse = (new ServerRequestHandler($apiClient))->handle(ServerRequest::fromGlobals());

(new SapiEmitter())->emit($apiResponse);

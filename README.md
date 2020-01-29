# SportySKY PHP client

**How to integrate SportySKY within your PHP project**

[![Build Status](https://travis-ci.org/SportRIZER/sportysky-client-php.svg?branch=master)](https://travis-ci.org/SportRIZER/sportysky-client-php)
[![codecov](https://codecov.io/gh/SportRIZER/sportysky-client-php/branch/master/graphs/badge.svg)](https://codecov.io/gh/SportRIZER/sportysky-client-php)
## Requirements

 - PHP ^7.2
 - [Composer](https://getcomposer.org/)

## Getting started

Install the SportySKY PHP client via the composer package manager :

``` bash
composer require sportrizer/sportysky-client-php
```

### Integration with the SportySKY javascript library

This library is developed to work seamlessly with the SportySKY javascript library provided by SportRIZER. 

Create a php script that will be called by the javascript library :

``` php
<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\ServerRequest;
use Sportrizer\Sportysky\ApiClient;
use Sportrizer\Sportysky\Authenticator;
use Sportrizer\Sportysky\ServerRequestHandler;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

require '../vendor/autoload.php';

// Authenticate the server to SportRIZER
$authenticator = new Authenticator(getenv('SPORTYSKY_CLIENT_ID'), getenv('SPORTYSKY_CLIENT_SECRET'));

// Create a new SportySKY API client
// with the JWT token provided by the authenticator
$apiClient = new ApiClient($authenticator->getToken());

// Handles the request made by the JS API
$apiResponse = (new ServerRequestHandler($apiClient))->handle(ServerRequest::fromGlobals());

// Outputs the SportySKY API response
(new SapiEmitter())->emit($apiResponse);
```

You should set your client ID ( `SPORTYSKY_CLIENT_ID` ) and client secret ( `SPORTYSKY_CLIENT_SECRET` ) in environment variables.

This script will authenticate your sever and return json from the SportySKY API that will be consumed by the javascript library.

## Caching

### SportySKY API responses

API calls are made by [Guzzle](https://github.com/guzzle/guzzle) which can be configured with the [Kevinrob's cache middleware](https://github.com/Kevinrob/guzzle-cache-middleware)

You can for example provide a [PSR-16](https://www.php-fig.org/psr/psr-16/) compatible Redis cache to the second argument of `ApiClient` : 

``` php
use Desarrolla2\Cache\Predis;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr16CacheStorage;
use Kevinrob\GuzzleCache\Strategy\PublicCacheStrategy;
use Predis\Client;
use Sportrizer\Sportysky\ApiClient;

// Authenticator ... 

$cacheHandler = HandlerStack::create();
$cacheHandler->push(new CacheMiddleware(
    new PublicCacheStrategy(
        new Psr16CacheStorage(
            new Predis(
                new Client(getenv('REDIS_URL')) // tcp://127.0.0.1:6379
            )
        )
    )
));

$apiClient = new ApiClient($authenticator->getToken(), $cacheHandler);
```

[See full example](examples/js-api-integration/redis-cached-api-proxy.php)

By default, the responses will be cached according to the cache headers provided by the API but you can define your own strategy : [See more examples](https://github.com/Kevinrob/guzzle-cache-middleware#examples)

Some other [PSR-16](https://www.php-fig.org/psr/psr-16/) adapters are already shipped with this library : 

https://github.com/desarrolla2/Cache#adapters

### JWT Authentication token

By default, the JWT token is cached in the temporary system directory until its expiration but you can provide your own [PSR-16](https://www.php-fig.org/psr/psr-16/) cache integration as the third argument of the `Authenticator` .

Exemple with Redis :

``` php
use Desarrolla2\Cache\Predis;
use Predis\Client;
use Sportrizer\Sportysky\Authenticator;

$redisCache = new Predis(new Client(getenv('REDIS_URL'))); // tcp://127.0.0.1:6379

$authenticator = new Authenticator(getenv('SPORTYSKY_CLIENT_ID'), getenv('SPORTYSKY_CLIENT_SECRET'), $redisCache);
```

## Examples

[Integration with the JS library](examples/)

## Testing

``` bash
composer test
```


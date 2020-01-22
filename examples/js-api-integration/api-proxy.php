<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\ServerRequest;
use Sportrizer\Sportysky\ApiClient;
use Sportrizer\Sportysky\Authenticator;
use Sportrizer\Sportysky\ServerRequestHandler;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

require '../vendor/autoload.php';

$authenticator = new Authenticator(getenv('SPORTYSKY_CLIENT_ID'), getenv('SPORTYSKY_CLIENT_SECRET'));

$apiClient = new ApiClient($authenticator->getToken());
$apiResponse = (new ServerRequestHandler($apiClient))->handle(ServerRequest::fromGlobals());

(new SapiEmitter())->emit($apiResponse);

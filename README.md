# SportySKY PHP client

**How to integrate SportySKY within your PHP project**

## Requirements

 - PHP ^7.2
 - [Composer](https://getcomposer.org/)

## Getting started

Install SportySKY PHP client via the composer package manager :

```bash
composer require sportrizer/sportysky-client-php
```

### Integration with the SportySKY javascript library

This library is developed to work seemlessly with the SportySKY javascript library provided by SportRIZER. 

Create a php script that will be called by the javascript library :

```php
<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\ServerRequest;
use Sportrizer\Sportysky\ApiClient;
use Sportrizer\Sportysky\Authenticator;
use Sportrizer\Sportysky\ServerRequestHandler;

require '../vendor/autoload.php';

$authenticator = new Authenticator(getenv('SPORTYSKY_CLIENT_ID'), getenv('SPORTYSKY_CLIENT_SECRET'));
$apiClient = new ApiClient($authenticator->getToken());
$apiResponse = (new ServerRequestHandler($apiClient))->handle(ServerRequest::fromGlobals());

echo json_encode($apiResponse);

```

You should set your client ID (`SPORTYSKY_CLIENT_ID`) and client secret (`SPORTYSKY_CLIENT_SECRET`) in environment variables.

This script will authenticate your sever and return json from the SportySKY API that will be consumed by the javascript library.

## Testing

```bash
vendor/bin/phpunit tests
```

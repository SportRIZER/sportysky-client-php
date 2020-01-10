<?php

namespace Sportrizer\Sportysky\Tests;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Handler\MockHandler;
use Sportrizer\Sportysky\ApiClient;
use Sportrizer\Sportysky\ServerRequestHandler;
use Sportrizer\Sportysky\Exception\BadRequestException;

class ServerRequestHandlerTest extends TestCase
{
    private const MOCK_DIRECTORY = __DIR__  . DIRECTORY_SEPARATOR . 'mock' . DIRECTORY_SEPARATOR;

    public function testShouldThrowExceptionOnInvalidMapView()
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Bad request');
        $this->expectExceptionCode(400);

        $serverRequestHandler = $this->getServerRequestHandler(400);

        $serverRequest = new ServerRequest('GET', '/api.php');
        $serverRequest = $serverRequest->withQueryParams([
            'mapView' => 'invalidView'
        ]);

        $result = $serverRequestHandler->handle($serverRequest);
    }

    public function testShouldReturnEmptyJsonResponse()
    {
        $serverRequestHandler = $this->getServerRequestHandler(200, '');

        $serverRequest = new ServerRequest('GET', '/api.php');

        $result = $serverRequestHandler->handle($serverRequest);

        $this->assertEquals('{}', $result->getBody()->getContents());
    }

    public function testShouldReturnForecastJson()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'forecast-data.json');

        $serverRequestHandler = $this->getServerRequestHandler(200, $body);

        $serverRequest = new ServerRequest('GET', '/api.php');
        $serverRequest = $serverRequest->withQueryParams([
            'mapView' => 'country'
        ]);

        $result = $serverRequestHandler->handle($serverRequest);

        $this->assertEquals($body, $result->getBody()->getContents());
    }

    private function getServerRequestHandler(int $status, $body = null)
    {
        $mock = new MockHandler([new Response($status, [], $body)]);
        $handler = HandlerStack::create($mock);

        return new ServerRequestHandler(new ApiClient('dummyToken', $handler));
    }
}

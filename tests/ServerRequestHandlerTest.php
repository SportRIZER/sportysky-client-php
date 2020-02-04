<?php

namespace Sportrizer\Sportysky\Tests;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Handler\MockHandler;
use Sportrizer\Sportysky\ApiClient;
use Sportrizer\Sportysky\ServerRequestHandler;

class ServerRequestHandlerTest extends TestCase
{
    private const MOCK_DIRECTORY = __DIR__  . DIRECTORY_SEPARATOR . 'mock' . DIRECTORY_SEPARATOR;

    public function testShouldThrowExceptionOnMissingMinDate()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);

        $serverRequestHandler = $this->getServerRequestHandler(400);

        $serverRequest = new ServerRequest('GET', '/api.php');
        $serverRequest = $serverRequest->withQueryParams([
            'minDate' => ''
        ]);

        $serverRequestHandler->handle($serverRequest);
    }

    public function testShouldReturnBadRequestResponse()
    {
        $serverRequestHandler = $this->getServerRequestHandler(200, '');

        $serverRequest = new ServerRequest('GET', '/api.php');

        $result = $serverRequestHandler->handle($serverRequest);

        $this->assertEquals('{"error":"Bad request"}', $result->getBody()->getContents());
    }

    public function testShouldReturnForecastJson()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'forecast-data.json');

        $serverRequestHandler = $this->getServerRequestHandler(200, $body);

        $serverRequest = new ServerRequest('GET', '/api.php');
        $serverRequest = $serverRequest->withQueryParams([
            'minDate' => '2020-01-14T17:36:00+00:00'
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

<?php

namespace Sportrizer\Sportysky\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Sportrizer\Sportysky\ApiClient;
use Sportrizer\Sportysky\Exception\BadRequestException;

class ApiClientTest extends TestCase
{
    private const MOCK_DIRECTORY = __DIR__  . DIRECTORY_SEPARATOR . 'mock' . DIRECTORY_SEPARATOR;

    public function testShouldThrowExceptionForMissingMinDate()
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Bad request');
        $this->expectExceptionCode(400);

        $apiClient = $this->getApiClient(400);

        $apiClient->getForecastResponse('');
    }

    public function testShouldReturnValidResponse()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'forecast-data.json');

        $apiClient = $this->getApiClient(200, $body);

        $result = $apiClient->getForecastResponse('2020-01-14T17:36:00+00:00');

        $this->assertEquals($body, $result->getBody()->getContents());
    }

    private function getApiClient(int $status, $body = null)
    {
        $mock = new MockHandler([new Response($status, [], $body)]);
        $handler = HandlerStack::create($mock);
        
        return new ApiClient('dummytoken', $handler);
    }
}
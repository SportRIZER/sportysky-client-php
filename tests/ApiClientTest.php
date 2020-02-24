<?php

namespace Sportrizer\Sportysky\Tests;

use DateTime;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use Sportrizer\Sportysky\ApiClient;
use GuzzleHttp\Exception\ClientException;
use Sportrizer\Sportysky\Utils\Geo\Box;
use Sportrizer\Sportysky\Utils\Geo\Point;

class ApiClientTest extends TestCase
{
    private const MOCK_DIRECTORY = __DIR__  . DIRECTORY_SEPARATOR . 'mock' . DIRECTORY_SEPARATOR;

    public function testShouldReturnValidResponse()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'forecast-data.json');

        $apiClient = $this->getApiClient(200, $body);

        $result = $apiClient->getForecastResponse(new DateTime(), null, null, null, 'FR');

        $this->assertEquals($body, $result->getBody()->getContents());
    }

    public function testShouldReturnValidCountryResponse()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'forecast-data.json');

        $apiClient = $this->getApiClient(200, $body);

        $result = $apiClient->getCountryForecastResponse('FR', new DateTime());

        $this->assertEquals($body, $result->getBody()->getContents());
    }

    public function testShouldReturnValidRegionResponse()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'forecast-data.json');

        $apiClient = $this->getApiClient(200, $body);

        $result = $apiClient->getRegionForecastResponse('FR-BRE', new DateTime());

        $this->assertEquals($body, $result->getBody()->getContents());
    }

    public function testShouldReturnValidDepartmentResponse()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'forecast-data.json');

        $apiClient = $this->getApiClient(200, $body);

        $result = $apiClient->getDepartmentForecastResponse('FR-29', new DateTime());

        $this->assertEquals($body, $result->getBody()->getContents());
    }

    public function testShouldReturnValidSpotResponse()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'forecast-data.json');

        $apiClient = $this->getApiClient(200, $body);

        $result = $apiClient->getSpotForecastResponse(
            '1234-1234-1234-1234',
            new DateTime(),
            new DateTime()
        );

        $this->assertEquals($body, $result->getBody()->getContents());
    }

    public function testShouldReturnValidSpotByIsoCodeResponse()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'forecast-data.json');

        $apiClient = $this->getApiClient(200, $body);

        $result = $apiClient->getSpotForecastByIsoCodeResponse(
            'iso-code',
            new DateTime(),
            new DateTime()
        );

        $this->assertEquals($body, $result->getBody()->getContents());
    }

    public function testShouldReturnValidSpotsResponse()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'spots.json');

        $apiClient = $this->getApiClient(200, $body);

        $result = $apiClient->getSpotsResponse(
            new Point(12.2, 13.3),
            new Box(new Point(14.4, 15.5), new Point(16.6, 17.7))
        );

        $this->assertEquals($body, $result->getBody()->getContents());
    }

    private function getApiClient(int $status, $body = null)
    {
        $mock = new MockHandler([new Response($status, [], $body)]);
        $handler = HandlerStack::create($mock);

        return new ApiClient('dummytoken', $handler);
    }
}

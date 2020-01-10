<?php

namespace Sportrizer\Sportysky\Tests;

use Desarrolla2\Cache\Memory;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Sportrizer\Sportysky\Authenticator;
use Sportrizer\Sportysky\Exception\AuthenticationException;

class AuthenticatorTest extends TestCase
{
    private const MOCK_DIRECTORY = __DIR__  . DIRECTORY_SEPARATOR . 'mock' . DIRECTORY_SEPARATOR;

    public function testShouldThrowExceptionOnAccessDenied()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'access-denied.json');
    
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Could not authenticate to SportRIZER');
        $this->expectExceptionCode(401);

        $authenticator = $this->getAuthenticator(401, $body);

        $authenticator->getToken();
    }

    public function testShouldThrowExceptionOnBadAuthResponse()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'bad-auth-response.json');
    
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Could not authenticate to SportRIZER');
        $this->expectExceptionCode(401);

        $authenticator = $this->getAuthenticator(401, $body);

        $authenticator->getToken();
    }

    public function testShouldReturnValidToken()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'authentication-success.json');

        $authenticator = $this->getAuthenticator(200, $body);

        $result = $authenticator->getToken();

        $this->assertEquals(json_decode($body, true)['access_token'], $result);
    }

    public function testShouldReturnCachedValidToken()
    {
        $body = file_get_contents(self::MOCK_DIRECTORY . 'authentication-success.json');

        $authenticator = $this->getAuthenticator(200, $body);

        $result = $authenticator->getToken();

        $this->assertEquals(json_decode($body, true)['access_token'], $result);

        $newResult = $authenticator->getToken();

        $this->assertEquals(json_decode($body, true)['access_token'], $newResult);
    }

    private function getAuthenticator(int $status, $body = null)
    {
        $mock = new MockHandler([new Response($status, [], $body)]);
        $handler = HandlerStack::create($mock);
        
        return new Authenticator('dummyClientId', 'dummyClientSecret', new Memory(), $handler);
    }
}

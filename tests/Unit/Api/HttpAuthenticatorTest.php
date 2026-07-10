<?php

declare(strict_types=1);

namespace Tests\Sylius\AdminMcpServerPlugin\Unit\Api;

use Sylius\AdminMcpServerPlugin\Api\HttpAuthenticator;
use Sylius\AdminMcpServerPlugin\Exception\AuthenticationFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class HttpAuthenticatorTest extends TestCase
{
    public function testReturnsTokenOnSuccess(): void
    {
        $client = new MockHttpClient(new MockResponse(
            json_encode(['token' => 'jwt-abc'], \JSON_THROW_ON_ERROR),
            ['http_code' => 200],
        ));

        self::assertSame('jwt-abc', (new HttpAuthenticator($client))->requestToken('admin@example.com', 'secret'));
    }

    public function testThrowsWhenResponseHasNoToken(): void
    {
        $client = new MockHttpClient(new MockResponse(
            json_encode(['foo' => 'bar'], \JSON_THROW_ON_ERROR),
            ['http_code' => 200],
        ));

        $this->expectException(AuthenticationFailedException::class);

        (new HttpAuthenticator($client))->requestToken('admin@example.com', 'secret');
    }

    public function testThrowsOnHttpError(): void
    {
        $client = new MockHttpClient(new MockResponse('', ['http_code' => 401]));

        $this->expectException(AuthenticationFailedException::class);

        (new HttpAuthenticator($client))->requestToken('admin@example.com', 'wrong');
    }
}

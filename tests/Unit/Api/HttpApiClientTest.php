<?php

declare(strict_types=1);

namespace Tests\Sylius\AdminMcpServerPlugin\Unit\Api;

use Sylius\AdminMcpServerPlugin\Api\HttpApiClient;
use Sylius\AdminMcpServerPlugin\Exception\NotAuthenticatedException;
use Sylius\AdminMcpServerPlugin\Provider\TokenProviderInterface;
use Mcp\Exception\ToolCallException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class HttpApiClientTest extends TestCase
{
    public function testAttachesBearerTokenAndReturnsBody(): void
    {
        $client = new MockHttpClient(function (string $method, string $url, array $options): MockResponse {
            self::assertContains('Authorization: Bearer tok-123', $options['headers']);

            return new MockResponse('RESPONSE_BODY');
        });

        $provider = $this->createMock(TokenProviderInterface::class);
        $provider->method('getToken')->willReturn('tok-123');

        $apiClient = new HttpApiClient($client, new MockHttpClient(), $provider);

        self::assertSame('RESPONSE_BODY', $apiClient->get('administrators'));
    }

    public function testDoesNotRetryAndReturnsBodyOnUnauthorized(): void
    {
        $client = new MockHttpClient([
            new MockResponse('UNAUTHORIZED_BODY', ['http_code' => 401]),
        ]);

        $provider = $this->createMock(TokenProviderInterface::class);
        $provider->expects(self::once())
            ->method('getToken')
            ->willReturn('tok-123');

        $apiClient = new HttpApiClient($client, new MockHttpClient(), $provider);

        self::assertSame('UNAUTHORIZED_BODY', $apiClient->get('administrators'));
    }

    public function testTranslatesNotAuthenticatedToToolCallException(): void
    {
        $provider = $this->createMock(TokenProviderInterface::class);
        $provider->method('getToken')->willThrowException(new NotAuthenticatedException('login first'));

        $apiClient = new HttpApiClient(new MockHttpClient(), new MockHttpClient(), $provider);

        $this->expectException(ToolCallException::class);
        $this->expectExceptionMessage('login first');

        $apiClient->get('administrators');
    }
}

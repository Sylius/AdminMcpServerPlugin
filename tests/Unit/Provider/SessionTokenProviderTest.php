<?php

declare(strict_types=1);

namespace Tests\Acme\SyliusExamplePlugin\Unit\Provider;

use Acme\SyliusExamplePlugin\Exception\NotAuthenticatedException;
use Acme\SyliusExamplePlugin\Provider\SessionTokenProvider;
use Acme\SyliusExamplePlugin\Session\TokenStorageInterface;
use PHPUnit\Framework\TestCase;

final class SessionTokenProviderTest extends TestCase
{
    public function testReturnsCachedToken(): void
    {
        $storage = $this->createMock(TokenStorageInterface::class);
        $storage->method('get')->willReturn('cached-token');

        self::assertSame('cached-token', (new SessionTokenProvider($storage))->getToken());
    }

    public function testThrowsWhenNoCachedToken(): void
    {
        $storage = $this->createMock(TokenStorageInterface::class);
        $storage->method('get')->willReturn(null);

        $this->expectException(NotAuthenticatedException::class);

        (new SessionTokenProvider($storage))->getToken();
    }

    public function testForceRefreshAlwaysThrowsWithoutTouchingStorage(): void
    {
        $storage = $this->createMock(TokenStorageInterface::class);
        $storage->expects(self::never())->method('get');

        $this->expectException(NotAuthenticatedException::class);

        (new SessionTokenProvider($storage))->getToken(forceRefresh: true);
    }
}

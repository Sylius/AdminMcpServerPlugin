<?php

declare(strict_types=1);

namespace Tests\Sylius\AdminMcpServerPlugin\Unit\Provider;

use Sylius\AdminMcpServerPlugin\Api\AuthenticatorInterface;
use Sylius\AdminMcpServerPlugin\Provider\CredentialsTokenProvider;
use Sylius\AdminMcpServerPlugin\Session\TokenStorageInterface;
use PHPUnit\Framework\TestCase;

final class CredentialsTokenProviderTest extends TestCase
{
    public function testReturnsCachedTokenWithoutAuthenticating(): void
    {
        $storage = $this->createMock(TokenStorageInterface::class);
        $storage->method('get')->willReturn('cached-token');

        $authenticator = $this->createMock(AuthenticatorInterface::class);
        $authenticator->expects(self::never())->method('requestToken');

        $provider = new CredentialsTokenProvider($storage, $authenticator, 'admin@example.com', 'secret');

        self::assertSame('cached-token', $provider->getToken());
    }

    public function testAcquiresAndStoresOnCacheMiss(): void
    {
        $storage = $this->createMock(TokenStorageInterface::class);
        $storage->method('get')->willReturn(null);
        $storage->expects(self::once())->method('store')->with('fresh-token');

        $authenticator = $this->createMock(AuthenticatorInterface::class);
        $authenticator->expects(self::once())
            ->method('requestToken')
            ->with('admin@example.com', 'secret')
            ->willReturn('fresh-token');

        $provider = new CredentialsTokenProvider($storage, $authenticator, 'admin@example.com', 'secret');

        self::assertSame('fresh-token', $provider->getToken());
    }

    public function testForceRefreshSkipsCacheAndAcquires(): void
    {
        $storage = $this->createMock(TokenStorageInterface::class);
        $storage->expects(self::never())->method('get');
        $storage->expects(self::once())->method('store')->with('fresh-token');

        $authenticator = $this->createMock(AuthenticatorInterface::class);
        $authenticator->method('requestToken')->willReturn('fresh-token');

        $provider = new CredentialsTokenProvider($storage, $authenticator, 'admin@example.com', 'secret');

        self::assertSame('fresh-token', $provider->getToken(forceRefresh: true));
    }
}

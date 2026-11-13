<?php

declare(strict_types=1);

namespace Tests\Sylius\AdminMcpServerPlugin\Unit\OAuth\Registration;

use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthClient;
use Sylius\AdminMcpServerPlugin\OAuth\Exception\OAuthException;
use Sylius\AdminMcpServerPlugin\OAuth\Registration\ClientRegistrar;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use PHPUnit\Framework\TestCase;

final class ClientRegistrarTest extends TestCase
{
    private ClientRegistrar $registrar;
    private OAuthClientRepository $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(OAuthClientRepository::class);
        $this->registrar = new ClientRegistrar($this->repository);
    }

    public function testRegistersClientWithValidData(): void
    {
        $data = [
            'client_name' => 'Test Client',
            'redirect_uris' => ['https://test.test/callback'],
        ];

        $this->repository->expects(self::once())
            ->method('save')
            ->with(self::isInstanceOf(OAuthClient::class));

        [$client, $secret] = $this->registrar->register($data);

        self::assertInstanceOf(OAuthClient::class, $client);
        self::assertSame('Test Client', $client->getClientName());
        self::assertSame(['https://test.test/callback'], $client->getRedirectUri());
    }

    public function testRegistersClientWithDefaultName(): void
    {
        $data = [
            'redirect_uris' => ['https://test.test/callback'],
        ];

        $this->repository->expects(self::once())
            ->method('save')
            ->with(self::isInstanceOf(OAuthClient::class));

        [$client] = $this->registrar->register($data);

        self::assertSame('MCP Client', $client->getClientName());
    }

    public function testThrowsExceptionWhenRedirectUrisAreMissing(): void
    {
        $this->expectException(OAuthException::class);
        $this->expectExceptionMessage('redirect_uris must be a list of strings');

        $this->registrar->register([
            'client_name' => 'Test Client',
        ]);
    }

    public function testThrowsExceptionWhenRedirectUrisAreInvalidUrls(): void
    {
        $this->expectException(OAuthException::class);
        $this->expectExceptionMessage('redirect_uris must be valid URLs');

        $this->registrar->register([
            'client_name' => 'Test Client',
            'redirect_uris' => ['not-a-url'],
        ]);
    }

    public function testThrowsExceptionWhenRedirectUrisAreEmpty(): void
    {
        $this->expectException(OAuthException::class);
        $this->expectExceptionMessage('redirect_uris must be a non-empty list of strings');

        $this->registrar->register([
            'client_name' => 'Test Client',
            'redirect_uris' => [],
        ]);
    }

    public function testThrowsExceptionWhenRedirectUrisAreNotArray(): void
    {
        $this->expectException(OAuthException::class);
        $this->expectExceptionMessage('redirect_uris must be a list of strings');

        $this->registrar->register([
            'client_name' => 'Test Client',
            'redirect_uris' => 'https://test.test/callback',
        ]);
    }

    public function testRegistersConfidentialClient(): void
    {
        $data = [
            'client_name' => 'Confidential Client',
            'redirect_uris' => ['https://test.test/callback'],
            'token_endpoint_auth_method' => 'client_secret_post',
        ];

        $this->repository->expects(self::once())
            ->method('save')
            ->with(self::isInstanceOf(OAuthClient::class));

        [$client, $secret] = $this->registrar->register($data);

        self::assertTrue($client->isConfidential());
        self::assertNotNull($secret);
        self::assertTrue($client->verifySecret($secret));
    }

    public function testRegistersPublicClient(): void
    {
        $data = [
            'client_name' => 'Public Client',
            'redirect_uris' => ['https://test.test/callback'],
            'token_endpoint_auth_method' => 'none',
        ];

        $this->repository->expects(self::once())
            ->method('save')
            ->with(self::isInstanceOf(OAuthClient::class));

        [$client, $secret] = $this->registrar->register($data);

        self::assertFalse($client->isConfidential());
        self::assertNull($secret);
    }

    public function testRegistersClientWithCustomGrantTypes(): void
    {
        $data = [
            'client_name' => 'Test Client',
            'redirect_uris' => ['https://test.test/callback'],
            'grant_types' => ['authorization_code'],
        ];

        $this->repository->expects(self::once())
            ->method('save')
            ->with(self::isInstanceOf(OAuthClient::class));

        [$client] = $this->registrar->register($data);

        self::assertSame(['authorization_code'], $client->getGrantTypes());
    }
}

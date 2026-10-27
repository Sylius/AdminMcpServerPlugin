<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\AdminMcpServerPlugin\Unit\Controller\OAuth;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\AdminMcpServerPlugin\Controller\OAuth\RegistrationController;
use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthClient;
use Sylius\AdminMcpServerPlugin\OAuth\Registration\ClientRegistrar;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RegistrationControllerTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;

    /** @var list<OAuthClient> */
    private array $persistedClients = [];

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getClassMetadata')->willReturn(new ClassMetadata(OAuthClient::class));
        $this->entityManager->method('persist')->willReturnCallback(function (object $entity): void {
            self::assertInstanceOf(OAuthClient::class, $entity);
            $this->persistedClients[] = $entity;
        });
    }

    public function testRegistersAPublicClientAndPersistsIt(): void
    {
        $this->entityManager->expects(self::once())->method('flush');

        $response = $this->register([
            'redirect_uris' => ['https://client.test/callback'],
            'client_name' => 'Claude Desktop',
        ]);

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $payload = $this->payloadOf($response);
        self::assertSame(['https://client.test/callback'], $payload['redirect_uris']);
        self::assertSame('Claude Desktop', $payload['client_name']);
        self::assertSame('none', $payload['token_endpoint_auth_method']);
        self::assertSame(['authorization_code', 'refresh_token'], $payload['grant_types']);
        self::assertArrayNotHasKey('client_secret', $payload, 'A public client must not be issued a secret.');

        self::assertCount(1, $this->persistedClients);
        self::assertSame($this->persistedClients[0]->getClientId(), $payload['client_id']);
    }

    public function testIssuesAVerifiableSecretForAConfidentialClient(): void
    {
        $response = $this->register([
            'redirect_uris' => ['https://client.test/callback'],
            'token_endpoint_auth_method' => 'client_secret_post',
        ]);

        $payload = $this->payloadOf($response);
        self::assertIsString($payload['client_secret']);
        self::assertSame(0, $payload['client_secret_expires_at']);

        $client = $this->persistedClients[0];
        self::assertTrue($client->isConfidential());
        self::assertTrue($client->verifySecret($payload['client_secret']), 'The returned secret must verify against the stored hash.');
        self::assertFalse($client->verifySecret('not-the-secret'));
    }

    public function testFallsBackToADefaultClientName(): void
    {
        $response = $this->register(['redirect_uris' => ['https://client.test/callback']]);

        self::assertSame('MCP Client', $this->payloadOf($response)['client_name']);
    }

    public function testHonoursExplicitlyRequestedGrantTypes(): void
    {
        $response = $this->register([
            'redirect_uris' => ['https://client.test/callback'],
            'grant_types' => ['authorization_code'],
        ]);

        self::assertSame(['authorization_code'], $this->payloadOf($response)['grant_types']);
    }

    public function testRejectsAMalformedJsonBody(): void
    {
        $response = $this->registerRaw('{"redirect_uris": [');

        $this->assertRejected($response, 'Invalid JSON body');
    }

    /** @return iterable<string, array{array<string, mixed>, string}> */
    public static function invalidMetadataProvider(): iterable
    {
        yield 'no redirect_uris at all' => [
            [],
            'redirect_uris is required',
        ];

        yield 'redirect_uris is not an array' => [
            ['redirect_uris' => 'https://client.test/callback'],
            'redirect_uris is required',
        ];

        yield 'redirect_uris is empty' => [
            ['redirect_uris' => []],
            'redirect_uris must be a non-empty list of strings',
        ];

        yield 'redirect_uris holds a non-string' => [
            ['redirect_uris' => ['https://client.test/callback', 42]],
            'redirect_uris must be a non-empty list of strings',
        ];

        yield 'grant_types is empty' => [
            ['redirect_uris' => ['https://client.test/callback'], 'grant_types' => []],
            'grant_types must be a non-empty list of strings',
        ];

        yield 'grant_types holds a non-string' => [
            ['redirect_uris' => ['https://client.test/callback'], 'grant_types' => [['authorization_code']]],
            'grant_types must be a non-empty list of strings',
        ];
    }

    /** @param array<string, mixed> $payload */
    #[DataProvider('invalidMetadataProvider')]
    public function testRejectsInvalidClientMetadata(array $payload, string $expectedDescription): void
    {
        $this->entityManager->expects(self::never())->method('flush');

        $this->assertRejected($this->register($payload), $expectedDescription);

        self::assertSame([], $this->persistedClients);
    }

    /** @param array<string, mixed> $payload */
    private function register(array $payload): JsonResponse
    {
        return $this->registerRaw((string) json_encode($payload));
    }

    private function registerRaw(string $body): JsonResponse
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($this->entityManager);

        $controller = new RegistrationController(new ClientRegistrar(new OAuthClientRepository($registry)));

        return $controller(Request::create('/mcp/admin/oauth/register', 'POST', content: $body));
    }

    private function assertRejected(JsonResponse $response, string $expectedDescription): void
    {
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame(
            ['error' => 'invalid_client_metadata', 'error_description' => $expectedDescription],
            $this->payloadOf($response),
        );
    }

    /** @return array<string, mixed> */
    private function payloadOf(JsonResponse $response): array
    {
        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $response->getContent(), true, flags: \JSON_THROW_ON_ERROR);

        return $payload;
    }
}

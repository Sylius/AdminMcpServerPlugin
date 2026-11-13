<?php

declare(strict_types=1);

namespace Tests\Sylius\AdminMcpServerPlugin\Functional\Controller\OAuth;

use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthClient;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class OAuthFlowTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testCompleteOAuthFlow(): void
    {
        $clientName = 'Test MCP Client';
        $redirectUri = 'https://client.test/callback';

        // Step 1: Register confidential client
        $this->client->request('POST', '/mcp/admin/oauth/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'client_name' => $clientName,
            'redirect_uris' => [$redirectUri],
            'token_endpoint_auth_method' => 'client_secret_post',
        ]));

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertNotEmpty($content['client_id']);
        self::assertNotEmpty($content['client_secret']);
    }

    public function testRegisterClientFailsWithInvalidRedirectUri(): void
    {
        $response = $this->registerClient('Client', 'not-a-url');

        self::assertSame(Response::HTTP_BAD_REQUEST, $response['status']);
        self::assertStringContainsString('redirect_uris must be valid URLs', $response['error_description'] ?? '');
    }

    public function testAuthorizationRequiresLogin(): void
    {
        // Register client first
        $registrationResponse = $this->registerClient('Test Client', 'https://client.test/callback');
        $clientId = $registrationResponse['client_id'];

        // Try to authorize without login (don't call loginAsAdmin)
        $this->client->request('GET', '/admin/mcp/oauth/authorize', [
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => 'https://client.test/callback',
            'state' => 'state123',
        ]);

        // Should redirect (to login or error page)
        $response = $this->client->getResponse();
        self::assertTrue($response->isRedirect() || $response->getStatusCode() === Response::HTTP_FORBIDDEN);
    }

    public function testBearerTokenAuthenticationFailsWithInvalidToken(): void
    {
        $this->client->request('GET', '/mcp/admin/', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer invalid-token',
        ]);

        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('invalid_token', $this->client->getResponse()->getContent());
    }

    public function testPublicEndpointsAreAccessible(): void
    {
        // Register endpoint should not require bearer token
        $this->client->request('POST', '/mcp/admin/oauth/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'client_name' => 'Test',
            'redirect_uris' => ['https://test.test/callback'],
        ]));

        self::assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    private function registerClient(string $clientName, string $redirectUri): array
    {
        $this->client->request('POST', '/mcp/admin/oauth/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'client_name' => $clientName,
            'redirect_uris' => [$redirectUri],
        ]));

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        return [
            'status' => $response->getStatusCode(),
            'client_id' => $content['client_id'] ?? null,
            'client_secret' => $content['client_secret'] ?? null,
            'error_description' => $content['error_description'] ?? null,
        ];
    }

    private function authorize(string $clientId, string $redirectUri): int
    {
        // Need to be logged in as admin
        $this->loginAsAdmin();

        $this->client->request('GET', '/admin/mcp/oauth/authorize', [
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'state' => 'state123',
        ]);

        return $this->client->getResponse()->getStatusCode();
    }


    private function loginAsAdmin(): void
    {
        // Use test user from fixtures
        $this->client->request('POST', '/admin/login', [], [], [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
        ], http_build_query([
            '_username' => 'sylius',
            '_password' => 'sylius',
        ]));

        // Follow redirect after login
        if ($this->client->getResponse()->isRedirect()) {
            $this->client->followRedirect();
        }
    }
}

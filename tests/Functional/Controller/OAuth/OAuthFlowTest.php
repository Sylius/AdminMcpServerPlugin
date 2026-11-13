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

        // Step 1: Register client
        $registrationResponse = $this->registerClient($clientName, $redirectUri);
        self::assertSame(Response::HTTP_CREATED, $registrationResponse['status']);

        $clientId = $registrationResponse['client_id'];
        $clientSecret = $registrationResponse['client_secret'];

        // Step 2: Authorize
        $authorizeResponse = $this->authorize($clientId, $redirectUri);
        self::assertSame(Response::HTTP_FOUND, $authorizeResponse);

        // Step 3: Get authorization code from redirect (simulate user approval)
        $authCode = $this->extractAuthCode($clientId);
        self::assertNotEmpty($authCode);

        // Step 4: Exchange code for token
        $tokenResponse = $this->exchangeAuthCodeForToken($clientId, $clientSecret, $authCode, $redirectUri);
        self::assertSame(Response::HTTP_OK, $tokenResponse['status']);

        $accessToken = $tokenResponse['access_token'];
        self::assertNotEmpty($accessToken);

        // Step 5: Use bearer token to access protected resource
        $this->client->request('GET', '/mcp/admin/', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $accessToken,
        ]);

        self::assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testRegisterClientFailsWithInvalidRedirectUri(): void
    {
        $response = $this->registerClient('Client', 'not-a-url');

        self::assertSame(Response::HTTP_BAD_REQUEST, $response['status']);
    }

    public function testAuthorizationFailsWithoutLogin(): void
    {
        // Register client first
        $registrationResponse = $this->registerClient('Test Client', 'https://client.test/callback');
        $clientId = $registrationResponse['client_id'];

        // Logout if logged in
        $this->client->request('GET', '/admin/logout');

        // Try to authorize without login
        $this->client->request('GET', '/admin/mcp/oauth/authorize', [
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => 'https://client.test/callback',
            'state' => 'state123',
        ]);

        // Should redirect to login
        self::assertTrue($this->client->getResponse()->isRedirect('/admin/login'));
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
        $this->client->request('POST', '/_mcp/oauth/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'client_name' => 'Test',
            'redirect_uris' => ['https://test.test/callback'],
        ]));

        self::assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    private function registerClient(string $clientName, string $redirectUri): array
    {
        $this->client->request('POST', '/_mcp/oauth/register', [], [], [
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

    private function extractAuthCode(string $clientId): string
    {
        // Simulate user approval of consent form
        $this->loginAsAdmin();

        $this->client->request('POST', '/admin/mcp/oauth/authorize', [
            'client_id' => $clientId,
            'redirect_uri' => 'https://client.test/callback',
        ]);

        $response = $this->client->getResponse();
        if (!$response->isRedirect()) {
            return '';
        }

        $location = $response->headers->get('Location');
        if (!$location || !preg_match('/code=([^&]+)/', $location, $matches)) {
            return '';
        }

        return $matches[1];
    }

    private function exchangeAuthCodeForToken(
        string $clientId,
        string $clientSecret,
        string $authCode,
        string $redirectUri,
    ): array {
        $this->client->request('POST', '/_mcp/oauth/token', [], [], [
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
        ], http_build_query([
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]));

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        return [
            'status' => $response->getStatusCode(),
            'access_token' => $content['access_token'] ?? null,
            'refresh_token' => $content['refresh_token'] ?? null,
        ];
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

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

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response as Psr7Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Sylius\AdminMcpServerPlugin\Controller\OAuth\AuthorizationController;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;

final class AuthorizationControllerTest extends TestCase
{
    private const REDIRECT_URI = 'https://client.test/callback';

    private AuthorizationServer&MockObject $authorizationServer;

    private Security&MockObject $security;

    private Environment&MockObject $twig;

    private CsrfTokenManagerInterface&MockObject $csrfTokenManager;

    /** @var array{template: string, context: array<array-key, mixed>}|null */
    private ?array $rendered = null;

    protected function setUp(): void
    {
        $this->authorizationServer = $this->createMock(AuthorizationServer::class);
        $this->security = $this->createMock(Security::class);
        $this->csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);

        $this->twig = $this->createMock(Environment::class);
        $this->twig->method('render')->willReturnCallback(
            function (string $template, array $context = []): string {
                $this->rendered = ['template' => $template, 'context' => $context];

                return 'RENDERED';
            },
        );
    }

    public function testRejectsAnAuthorizationRequestTheServerRefuses(): void
    {
        $exception = OAuthServerException::invalidRequest('client_id', 'Client authentication failed');
        $this->authorizationServer->method('validateAuthorizationRequest')->willThrowException($exception);

        $response = $this->authorize($this->consentRequest());

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame($exception->getMessage(), $response->getContent());
    }

    public function testRedirectsAnonymousVisitorsToTheAdminLogin(): void
    {
        $this->authorizationServer->method('validateAuthorizationRequest')->willReturn($this->authorizationRequest());
        $this->security->method('getUser')->willReturn(null);

        $request = $this->consentRequest();
        $response = $this->authorize($request);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('/login?_target_path=' . urlencode($request->getUri()), $response->getTargetUrl());
    }

    public function testRefusesConsentToAnAdminWithoutApiAccess(): void
    {
        $this->authorizationServer->method('validateAuthorizationRequest')->willReturn($this->authorizationRequest());
        $this->security->method('getUser')->willReturn($this->adminUser(roles: ['ROLE_ADMINISTRATION_ACCESS']));

        $this->authorize($this->consentRequest());

        self::assertSame('@SyliusAdminMcpServer/oauth/error.html.twig', $this->renderedTemplate());
        self::assertSame('sylius_admin_mcp_server.oauth.error.no_api_access', $this->renderedContext()['message']);
        self::assertSame(
            self::REDIRECT_URI . '?error=access_denied&error_description=User+does+not+have+API+access',
            $this->renderedContext()['redirect_url'],
        );
    }

    public function testShowsTheConsentScreenToAnAdminWithApiAccess(): void
    {
        $this->authorizationServer->method('validateAuthorizationRequest')
            ->willReturn($this->authorizationRequest(scopes: ['admin_api', 'admin_read']));
        $this->security->method('getUser')->willReturn($this->adminUser());

        $response = $this->authorize($this->consentRequest(['state' => 'opaque-state', 'code_challenge' => 'chal']));

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('@SyliusAdminMcpServer/oauth/authorize.html.twig', $this->renderedTemplate());

        $context = $this->renderedContext();
        self::assertSame('Claude Desktop', $context['client_name']);
        self::assertSame('admin_api admin_read', $context['scope']);
        self::assertSame('opaque-state', $context['state']);
        self::assertSame('chal', $context['code_challenge']);
        self::assertSame(self::REDIRECT_URI, $context['redirect_uri']);
    }

    public function testRejectsAConsentSubmissionWithAnInvalidCsrfToken(): void
    {
        $this->csrfTokenManager->method('isTokenValid')->willReturn(false);
        $this->authorizationServer->expects(self::never())->method('validateAuthorizationRequest');

        $response = $this->authorize($this->approvalRequest());

        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        self::assertSame('Invalid CSRF token.', $response->getContent());
    }

    public function testCompletesAnApprovedAuthorizationForTheLoggedInAdmin(): void
    {
        $this->csrfTokenManager->method('isTokenValid')->willReturn(true);
        $this->authorizationServer->method('validateAuthorizationRequest')->willReturn($this->authorizationRequest());
        $this->security->method('getUser')->willReturn($this->adminUser(email: 'admin@example.com'));

        $this->authorizationServer->expects(self::once())
            ->method('completeAuthorizationRequest')
            ->willReturnCallback(function (AuthorizationRequest $authRequest): ResponseInterface {
                self::assertTrue($authRequest->isAuthorizationApproved());
                self::assertSame('admin@example.com', $authRequest->getUser()?->getIdentifier());

                return (new Psr7Response())->withHeader('Location', self::REDIRECT_URI . '?code=auth-code');
            });

        $response = $this->authorize($this->approvalRequest());

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('@SyliusAdminMcpServer/oauth/success.html.twig', $this->renderedTemplate());
        self::assertSame(self::REDIRECT_URI . '?code=auth-code', $this->renderedContext()['redirect_url']);
    }

    public function testMarksTheAuthorizationAsDeniedWhenTheAdminRejectsIt(): void
    {
        $this->csrfTokenManager->method('isTokenValid')->willReturn(true);
        $this->authorizationServer->method('validateAuthorizationRequest')->willReturn($this->authorizationRequest());
        $this->security->method('getUser')->willReturn($this->adminUser());

        $this->authorizationServer->expects(self::once())
            ->method('completeAuthorizationRequest')
            ->willReturnCallback(function (AuthorizationRequest $authRequest): ResponseInterface {
                self::assertFalse($authRequest->isAuthorizationApproved());

                throw OAuthServerException::accessDenied('The user denied the request', self::REDIRECT_URI);
            });

        $this->authorize($this->approvalRequest(['approve' => '0']));

        self::assertSame('@SyliusAdminMcpServer/oauth/error.html.twig', $this->renderedTemplate());
        self::assertSame('sylius_admin_mcp_server.oauth.error.denied_by_user', $this->renderedContext()['message']);
    }

    public function testRefusesToIssueACodeForAnAdminWithoutAnEmailAddress(): void
    {
        $this->csrfTokenManager->method('isTokenValid')->willReturn(true);
        $this->authorizationServer->method('validateAuthorizationRequest')->willReturn($this->authorizationRequest());
        $this->security->method('getUser')->willReturn($this->adminUser(email: null));
        $this->authorizationServer->expects(self::never())->method('completeAuthorizationRequest');

        $response = $this->authorize($this->approvalRequest());

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame('Admin user has no e-mail address.', $response->getContent());
    }

    public function testRefusesAConsentSubmissionFromAnAdminWithoutApiAccess(): void
    {
        $this->csrfTokenManager->method('isTokenValid')->willReturn(true);
        $this->authorizationServer->method('validateAuthorizationRequest')->willReturn($this->authorizationRequest());
        $this->security->method('getUser')->willReturn($this->adminUser(roles: ['ROLE_ADMINISTRATION_ACCESS']));
        $this->authorizationServer->expects(self::never())->method('completeAuthorizationRequest');

        $this->authorize($this->approvalRequest());

        self::assertSame('sylius_admin_mcp_server.oauth.error.no_api_access', $this->renderedContext()['message']);
    }

    private function authorize(Request $request): Response
    {
        $controller = new AuthorizationController(
            $this->authorizationServer,
            $this->security,
            $this->twig,
            $this->urlGenerator(),
            new PsrHttpFactory(...array_fill(0, 4, $this->psrFactory())),
            $this->csrfTokenManager,
        );

        return $controller->authorize($request);
    }

    /** @param array<string, string> $extraQuery */
    private function consentRequest(array $extraQuery = []): Request
    {
        return Request::create('https://shop.test/admin/mcp/oauth/authorize', 'GET', [
            'response_type' => 'code',
            'client_id' => 'client-123',
            'redirect_uri' => self::REDIRECT_URI,
        ] + $extraQuery);
    }

    /** @param array<string, string> $extraBody */
    private function approvalRequest(array $extraBody = []): Request
    {
        return Request::create('https://shop.test/admin/mcp/oauth/authorize', 'POST', $extraBody + [
            'approve' => '1',
            '_csrf_token' => 'token',
            'client_id' => 'client-123',
            'redirect_uri' => self::REDIRECT_URI,
        ]);
    }

    /** @param list<string> $scopes */
    private function authorizationRequest(array $scopes = ['admin_api']): AuthorizationRequest
    {
        $client = $this->createMock(ClientEntityInterface::class);
        $client->method('getName')->willReturn('Claude Desktop');

        $authRequest = new AuthorizationRequest();
        $authRequest->setClient($client);
        $authRequest->setRedirectUri(self::REDIRECT_URI);
        $authRequest->setScopes(array_map(
            function (string $identifier): ScopeEntityInterface {
                $scope = $this->createMock(ScopeEntityInterface::class);
                $scope->method('getIdentifier')->willReturn($identifier);

                return $scope;
            },
            $scopes,
        ));

        return $authRequest;
    }

    /** @param list<string> $roles */
    private function adminUser(?string $email = 'admin@example.com', array $roles = ['ROLE_API_ACCESS']): AdminUserInterface
    {
        $user = $this->createMock(AdminUserInterface::class);
        $user->method('getEmail')->willReturn($email);
        $user->method('getRoles')->willReturn($roles);

        return $user;
    }

    private function urlGenerator(): UrlGeneratorInterface
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturnCallback(
            static fn (string $route, array $parameters = []): string => $parameters === []
                ? '/login'
                : '/login?' . http_build_query($parameters),
        );

        return $urlGenerator;
    }

    private function psrFactory(): Psr17Factory
    {
        return new Psr17Factory();
    }

    private function renderedTemplate(): string
    {
        self::assertNotNull($this->rendered, 'No template was rendered.');

        return $this->rendered['template'];
    }

    /** @return array<array-key, mixed> */
    private function renderedContext(): array
    {
        self::assertNotNull($this->rendered, 'No template was rendered.');

        return $this->rendered['context'];
    }
}

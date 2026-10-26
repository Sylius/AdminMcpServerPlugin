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

namespace Tests\Sylius\AdminMcpServerPlugin\Unit\Security;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\AdminMcpServerPlugin\Security\Mcp\McpBearerAuthListener;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class McpBearerAuthListenerTest extends TestCase
{
    private ResourceServer&MockObject $resourceServer;

    /** @var UserRepositoryInterface<AdminUserInterface>&MockObject */
    private UserRepositoryInterface&MockObject $adminUserRepository;

    protected function setUp(): void
    {
        $this->resourceServer = $this->createMock(ResourceServer::class);
        /** @var UserRepositoryInterface<AdminUserInterface>&MockObject $adminUserRepository */
        $adminUserRepository = $this->createMock(UserRepositoryInterface::class);
        $this->adminUserRepository = $adminUserRepository;
    }

    public function testIgnoresRequestsOutsideTheMcpAdminPath(): void
    {
        $this->resourceServer->expects(self::never())->method('validateAuthenticatedRequest');

        $event = $this->dispatch(Request::create('/admin/products'));

        self::assertNull($event->getResponse());
    }

    /** @return iterable<string, array{string}> */
    public static function publicPathProvider(): iterable
    {
        yield 'dynamic client registration' => ['/mcp/admin/oauth/register'];
        yield 'token endpoint' => ['/mcp/admin/oauth/token'];
    }

    #[DataProvider('publicPathProvider')]
    public function testLetsPublicOAuthEndpointsThroughWithoutAToken(string $path): void
    {
        $this->resourceServer->expects(self::never())->method('validateAuthenticatedRequest');

        $event = $this->dispatch(Request::create($path, 'POST'));

        self::assertNull($event->getResponse());
    }

    public function testRejectsAProtectedRequestWithoutAnAuthorizationHeader(): void
    {
        $this->resourceServer->expects(self::never())->method('validateAuthenticatedRequest');

        $event = $this->dispatch(Request::create('http://shop.test/mcp/admin', 'POST'));

        $response = $this->assertUnauthorized($event);
        self::assertSame(['error' => 'unauthorized', 'error_description' => 'Bearer token required'], $this->payloadOf($response));
        self::assertSame(
            'Bearer resource_metadata="http://shop.test/.well-known/oauth-protected-resource"',
            $response->headers->get('WWW-Authenticate'),
        );
    }

    public function testRejectsAnAuthorizationHeaderThatIsNotABearerToken(): void
    {
        $this->resourceServer->expects(self::never())->method('validateAuthenticatedRequest');

        $request = Request::create('/mcp/admin', 'POST');
        $request->headers->set('Authorization', 'Basic dXNlcjpwYXNz');

        $response = $this->assertUnauthorized($this->dispatch($request));

        self::assertSame('unauthorized', $this->payloadOf($response)['error']);
    }

    public function testRejectsATokenRefusedByTheResourceServer(): void
    {
        $exception = OAuthServerException::accessDenied('The access token is invalid');
        $this->resourceServer->method('validateAuthenticatedRequest')->willThrowException($exception);

        $response = $this->assertUnauthorized($this->dispatch($this->bearerRequest('expired-token')));

        $payload = $this->payloadOf($response);
        self::assertSame('invalid_token', $payload['error']);
        self::assertSame($exception->getMessage(), $payload['error_description']);
        self::assertSame('Bearer error="invalid_token"', $response->headers->get('WWW-Authenticate'));
    }

    public function testRejectsAValidTokenWhoseUserNoLongerExists(): void
    {
        $this->resourceServer->method('validateAuthenticatedRequest')
            ->willReturn($this->validatedRequestFor('ghost@example.com'));
        $this->adminUserRepository->method('findOneByEmail')->with('ghost@example.com')->willReturn(null);

        $event = $this->dispatch($this->bearerRequest('valid-token'));
        $response = $this->assertUnauthorized($event);

        self::assertSame('Invalid token user', $this->payloadOf($response)['error_description']);
        self::assertFalse($event->getRequest()->attributes->has('_mcp_oauth_admin_user'));
    }

    /**
     * Regression guard: the identifier used to be cast with `(string)`, so a token carrying
     * no `oauth_user_id` searched the repository for an empty e-mail instead of being refused.
     */
    public function testRejectsAValidTokenCarryingNoUserIdentifier(): void
    {
        $this->resourceServer->method('validateAuthenticatedRequest')
            ->willReturn(new ServerRequest('POST', '/mcp/admin'));
        $this->adminUserRepository->expects(self::never())->method('findOneByEmail');

        $response = $this->assertUnauthorized($this->dispatch($this->bearerRequest('token-without-subject')));

        self::assertSame('Invalid token user', $this->payloadOf($response)['error_description']);
    }

    public function testRejectsAValidTokenCarryingAnEmptyUserIdentifier(): void
    {
        $this->resourceServer->method('validateAuthenticatedRequest')
            ->willReturn($this->validatedRequestFor(''));
        $this->adminUserRepository->expects(self::never())->method('findOneByEmail');

        $this->assertUnauthorized($this->dispatch($this->bearerRequest('token-with-empty-subject')));
    }

    public function testExposesTheAdminUserOnTheRequestForAValidToken(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $this->resourceServer->method('validateAuthenticatedRequest')
            ->willReturn($this->validatedRequestFor('admin@example.com'));
        $this->adminUserRepository->method('findOneByEmail')->with('admin@example.com')->willReturn($adminUser);

        $event = $this->dispatch($this->bearerRequest('valid-token'));

        self::assertNull($event->getResponse());
        self::assertSame($adminUser, $event->getRequest()->attributes->get('_mcp_oauth_admin_user'));
    }

    private function dispatch(Request $request): RequestEvent
    {
        $psr17Factory = new Psr17Factory();
        $listener = new McpBearerAuthListener(
            $this->resourceServer,
            new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory),
            $this->adminUserRepository,
        );

        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $listener($event);

        return $event;
    }

    private function bearerRequest(string $token): Request
    {
        $request = Request::create('/mcp/admin', 'POST');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        return $request;
    }

    private function validatedRequestFor(string $userIdentifier): ServerRequest
    {
        return (new ServerRequest('POST', '/mcp/admin'))->withAttribute('oauth_user_id', $userIdentifier);
    }

    private function assertUnauthorized(RequestEvent $event): JsonResponse
    {
        $response = $event->getResponse();

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        return $response;
    }

    /** @return array<string, mixed> */
    private function payloadOf(JsonResponse $response): array
    {
        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $response->getContent(), true, flags: \JSON_THROW_ON_ERROR);

        return $payload;
    }
}

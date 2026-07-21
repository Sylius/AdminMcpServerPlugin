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

namespace Sylius\AdminMcpServerPlugin\Security\Mcp;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final readonly class McpBearerAuthListener
{
    private const array PUBLIC_PATHS = [
        '/_mcp/oauth/register',
        '/_mcp/oauth/token',
    ];

    /**
     * @param UserRepositoryInterface<AdminUserInterface> $adminUserRepository
     */
    public function __construct(
        private ResourceServer $resourceServer,
        private PsrHttpFactory $psrHttpFactory,
        private UserRepositoryInterface $adminUserRepository,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        if (!str_starts_with($path, '/_mcp')) {
            return;
        }

        if ($this->isPublicPath($path)) {
            return;
        }

        $authHeader = (string) $request->headers->get('Authorization', '');

        if (!str_starts_with($authHeader, 'Bearer ')) {
            $event->setResponse($this->unauthorizedResponse($request->getSchemeAndHttpHost()));

            return;
        }

        $psrRequest = $this->psrHttpFactory->createRequest($request);

        try {
            $validatedRequest = $this->resourceServer->validateAuthenticatedRequest($psrRequest);
        } catch (OAuthServerException $e) {
            $event->setResponse($this->tokenErrorResponse($e->getMessage()));

            return;
        }

        $userIdentifier = (string) $validatedRequest->getAttribute('oauth_user_id', '');
        $adminUser = $this->adminUserRepository->findOneByEmail($userIdentifier);

        if ($adminUser === null) {
            $event->setResponse($this->tokenErrorResponse('Invalid token user'));

            return;
        }

        $request->attributes->set('_mcp_oauth_admin_user', $adminUser);
    }

    private function isPublicPath(string $path): bool
    {
        return \in_array($path, self::PUBLIC_PATHS, true);
    }

    private function unauthorizedResponse(string $host): JsonResponse
    {
        $response = new JsonResponse(
            ['error' => 'unauthorized', 'error_description' => 'Bearer token required'],
            Response::HTTP_UNAUTHORIZED,
        );
        $response->headers->set(
            'WWW-Authenticate',
            sprintf('Bearer resource_metadata="%s/.well-known/oauth-protected-resource"', $host),
        );

        return $response;
    }

    private function tokenErrorResponse(string $description): JsonResponse
    {
        $response = new JsonResponse(
            ['error' => 'invalid_token', 'error_description' => $description],
            Response::HTTP_UNAUTHORIZED,
        );
        $response->headers->set('WWW-Authenticate', 'Bearer error="invalid_token"');

        return $response;
    }
}

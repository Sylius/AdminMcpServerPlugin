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

namespace Sylius\AdminMcpServerPlugin\Provider;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sylius\AdminMcpServerPlugin\Exception\NotAuthenticatedException;
use Sylius\AdminMcpServerPlugin\Session\TokenStorageInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class OAuthJwtTokenProvider implements TokenProviderInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private RequestStack $requestStack,
        private TokenStorageInterface $storage,
    ) {
    }

    public function getToken(bool $forceRefresh = false): string
    {
        if (!$forceRefresh) {
            $cached = $this->storage->get();
            if ($cached !== null) {
                return $cached;
            }
        }

        $request = $this->requestStack->getCurrentRequest();
        $adminUser = $request?->attributes->get('_mcp_oauth_admin_user');

        if (!$adminUser instanceof AdminUserInterface) {
            throw new NotAuthenticatedException(
                'Authenticate via OAuth browser flow first (HTTP transport) or use the login tool (stdio transport).',
            );
        }

        $jwt = $this->jwtManager->create($adminUser);
        $this->storage->store($jwt);

        return $jwt;
    }
}

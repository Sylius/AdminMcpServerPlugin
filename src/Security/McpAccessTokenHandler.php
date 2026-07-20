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

namespace Sylius\AdminMcpServerPlugin\Security;

use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAccessTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final readonly class McpAccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private OAuthAccessTokenRepository $accessTokenRepository,
        private TokenHasher $tokenHasher,
    ) {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $token = $this->accessTokenRepository->findActiveByTokenHash($this->tokenHasher->hash($accessToken));

        if ($token === null) {
            throw new BadCredentialsException('Invalid or expired access token.');
        }

        return new UserBadge($token->getAdminUser()->getEmail() ?? '');
    }
}

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

namespace Sylius\AdminMcpServerPlugin\OAuth\Grant;

use Sylius\AdminMcpServerPlugin\OAuth\Exception\OAuthException;
use Sylius\AdminMcpServerPlugin\OAuth\IssuedTokenPair;
use Sylius\AdminMcpServerPlugin\OAuth\TokenIssuer;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthRefreshTokenRepository;
use Sylius\AdminMcpServerPlugin\Security\OAuth\TokenHasher;

final readonly class RefreshTokenGrantHandler
{
    public function __construct(
        private OAuthClientRepository $clientRepository,
        private OAuthRefreshTokenRepository $refreshTokenRepository,
        private TokenIssuer $tokenIssuer,
        private TokenHasher $tokenHasher,
    ) {
    }

    public function handle(string $clientId, string $plainRefreshToken): IssuedTokenPair
    {
        $this->clientRepository->findByClientId($clientId)
            ?? throw new OAuthException('invalid_client', 'Unknown client');

        $refreshToken = $this->refreshTokenRepository->findActiveByTokenHash($this->tokenHasher->hash($plainRefreshToken));
        if ($refreshToken === null) {
            throw new OAuthException('invalid_grant', 'Invalid or expired refresh token');
        }

        $oldAccessToken = $refreshToken->getAccessToken();
        if ($oldAccessToken->getClient()->getClientId() !== $clientId) {
            throw new OAuthException('invalid_grant', 'Client mismatch');
        }

        $refreshToken->revoke();
        $oldAccessToken->revoke();

        return $this->tokenIssuer->issue($oldAccessToken->getClient(), $oldAccessToken->getAdminUser(), $oldAccessToken->getScopes());
    }
}

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
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAuthorizationCodeRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Sylius\AdminMcpServerPlugin\Security\OAuth\PkceVerifier;
use Sylius\AdminMcpServerPlugin\Security\OAuth\TokenHasher;

final readonly class AuthorizationCodeGrantHandler
{
    public function __construct(
        private OAuthClientRepository $clientRepository,
        private OAuthAuthorizationCodeRepository $codeRepository,
        private PkceVerifier $pkceVerifier,
        private TokenIssuer $tokenIssuer,
        private TokenHasher $tokenHasher,
    ) {
    }

    public function handle(string $clientId, string $code, string $redirectUri, string $codeVerifier): IssuedTokenPair
    {
        $client = $this->clientRepository->findByClientId($clientId)
            ?? throw new OAuthException('invalid_client', 'Unknown client');

        $authCode = $this->codeRepository->findActiveByCodeHash($this->tokenHasher->hash($code));
        if ($authCode === null || $authCode->isUsed()) {
            throw new OAuthException('invalid_grant', 'Invalid or expired authorization code');
        }

        if ($authCode->getClient()->getClientId() !== $clientId) {
            throw new OAuthException('invalid_grant', 'Client mismatch');
        }

        if ($authCode->getRedirectUri() !== $redirectUri) {
            throw new OAuthException('invalid_grant', 'Redirect URI mismatch');
        }

        if (!($this->pkceVerifier)($codeVerifier, $authCode->getCodeChallenge())) {
            throw new OAuthException('invalid_grant', 'Invalid code_verifier');
        }

        $authCode->markUsed();

        return $this->tokenIssuer->issue($client, $authCode->getAdminUser(), $authCode->getScopes());
    }
}

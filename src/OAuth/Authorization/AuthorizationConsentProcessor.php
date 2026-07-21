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

namespace Sylius\AdminMcpServerPlugin\OAuth\Authorization;

use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthClient;
use Sylius\AdminMcpServerPlugin\OAuth\AuthorizationCodeIssuer;
use Sylius\AdminMcpServerPlugin\OAuth\Exception\OAuthException;
use Sylius\AdminMcpServerPlugin\OAuth\OAuthCallbackUrlBuilder;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Sylius\Component\Core\Model\AdminUserInterface;

final readonly class AuthorizationConsentProcessor
{
    public function __construct(
        private OAuthClientRepository $clientRepository,
        private AuthorizationCodeIssuer $codeIssuer,
        private OAuthCallbackUrlBuilder $callbackUrlBuilder,
    ) {
    }

    public function resolveClient(string $responseType, string $clientId, string $redirectUri): OAuthClient
    {
        if ($responseType !== 'code') {
            throw new OAuthException('unsupported_response_type', 'Only response_type=code is supported');
        }

        $client = $this->clientRepository->findByClientId($clientId);
        if ($client === null) {
            throw new OAuthException('invalid_client', 'Invalid client_id');
        }

        if (!$client->matchesRedirectUri($redirectUri)) {
            throw new OAuthException('invalid_request', 'Invalid redirect_uri');
        }

        return $client;
    }

    public function validatePkce(string $codeChallenge, string $codeChallengeMethod): void
    {
        if ($codeChallenge === '' || $codeChallengeMethod !== 'S256') {
            throw new OAuthException('invalid_request', 'PKCE with S256 is required');
        }
    }

    public function hasApiAccess(AdminUserInterface $user): bool
    {
        return \in_array('ROLE_API_ACCESS', $user->getRoles(), true);
    }

    public function grantConsent(
        OAuthClient $client,
        AdminUserInterface $adminUser,
        string $redirectUri,
        string $scope,
        string $state,
        string $codeChallenge,
        string $codeChallengeMethod,
    ): string {
        $plainCode = $this->codeIssuer->issue(
            $client,
            $adminUser,
            $redirectUri,
            $scope,
            $codeChallenge,
            $codeChallengeMethod,
        );

        return $this->callbackUrlBuilder->buildSuccessUrl($redirectUri, $plainCode, $state);
    }

    public function buildErrorUrl(string $redirectUri, string $state, string $error, string $description): string
    {
        return $this->callbackUrlBuilder->buildErrorUrl($redirectUri, $state, $error, $description);
    }
}

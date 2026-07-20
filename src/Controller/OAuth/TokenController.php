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

namespace Sylius\AdminMcpServerPlugin\Controller\OAuth;

use Sylius\AdminMcpServerPlugin\OAuth\IssuedTokenPair;
use Sylius\AdminMcpServerPlugin\OAuth\TokenIssuer;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthAuthorizationCodeRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthRefreshTokenRepository;
use Sylius\AdminMcpServerPlugin\Security\PkceVerifier;
use Sylius\AdminMcpServerPlugin\Security\TokenHasher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class TokenController
{
    public function __construct(
        private OAuthClientRepository $clientRepository,
        private OAuthAuthorizationCodeRepository $codeRepository,
        private OAuthRefreshTokenRepository $refreshTokenRepository,
        private PkceVerifier $pkceVerifier,
        private TokenIssuer $tokenIssuer,
        private TokenHasher $tokenHasher,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $grantType = $request->request->getString('grant_type');

        $response = match ($grantType) {
            'authorization_code' => $this->exchangeAuthorizationCode($request),
            'refresh_token' => $this->exchangeRefreshToken($request),
            default => $this->error('unsupported_grant_type', 'Grant type not supported'),
        };

        $response->headers->set('Cache-Control', 'no-store');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }

    private function exchangeAuthorizationCode(Request $request): JsonResponse
    {
        $clientId = $request->request->getString('client_id');
        $code = $request->request->getString('code');
        $redirectUri = $request->request->getString('redirect_uri');
        $codeVerifier = $request->request->getString('code_verifier');

        $client = $this->clientRepository->findByClientId($clientId);
        if ($client === null) {
            return $this->error('invalid_client', 'Unknown client');
        }

        $authCode = $this->codeRepository->findActiveByCodeHash($this->tokenHasher->hash($code));
        if ($authCode === null || $authCode->isUsed()) {
            return $this->error('invalid_grant', 'Invalid or expired authorization code');
        }

        if ($authCode->getClient()->getClientId() !== $clientId) {
            return $this->error('invalid_grant', 'Client mismatch');
        }

        if ($authCode->getRedirectUri() !== $redirectUri) {
            return $this->error('invalid_grant', 'Redirect URI mismatch');
        }

        if (!($this->pkceVerifier)($codeVerifier, $authCode->getCodeChallenge())) {
            return $this->error('invalid_grant', 'Invalid code_verifier');
        }

        $authCode->markUsed();

        return $this->tokenResponse(
            $this->tokenIssuer->issue($client, $authCode->getAdminUser(), $authCode->getScopes()),
        );
    }

    private function exchangeRefreshToken(Request $request): JsonResponse
    {
        $clientId = $request->request->getString('client_id');
        $plainRefreshToken = $request->request->getString('refresh_token');

        $client = $this->clientRepository->findByClientId($clientId);
        if ($client === null) {
            return $this->error('invalid_client', 'Unknown client');
        }

        $refreshToken = $this->refreshTokenRepository->findActiveByTokenHash($this->tokenHasher->hash($plainRefreshToken));
        if ($refreshToken === null) {
            return $this->error('invalid_grant', 'Invalid or expired refresh token');
        }

        $oldAccessToken = $refreshToken->getAccessToken();
        if ($oldAccessToken->getClient()->getClientId() !== $clientId) {
            return $this->error('invalid_grant', 'Client mismatch');
        }

        $refreshToken->revoke();
        $oldAccessToken->revoke();

        return $this->tokenResponse(
            $this->tokenIssuer->issue($client, $oldAccessToken->getAdminUser(), $oldAccessToken->getScopes()),
        );
    }

    private function tokenResponse(IssuedTokenPair $pair): JsonResponse
    {
        return new JsonResponse([
            'access_token' => $pair->accessToken,
            'token_type' => 'Bearer',
            'expires_in' => $pair->expiresIn,
            'refresh_token' => $pair->refreshToken,
            'scope' => $pair->scope,
        ]);
    }

    private function error(string $error, string $description): JsonResponse
    {
        return new JsonResponse(['error' => $error, 'error_description' => $description], Response::HTTP_BAD_REQUEST);
    }
}

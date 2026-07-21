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

use Sylius\AdminMcpServerPlugin\OAuth\Exception\OAuthException;
use Sylius\AdminMcpServerPlugin\OAuth\Grant\AuthorizationCodeGrantHandler;
use Sylius\AdminMcpServerPlugin\OAuth\Grant\RefreshTokenGrantHandler;
use Sylius\AdminMcpServerPlugin\OAuth\IssuedTokenPair;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class TokenController
{
    public function __construct(
        private AuthorizationCodeGrantHandler $authCodeHandler,
        private RefreshTokenGrantHandler $refreshTokenHandler,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $grantType = $request->request->getString('grant_type');

        try {
            $pair = match ($grantType) {
                'authorization_code' => $this->authCodeHandler->handle(
                    $request->request->getString('client_id'),
                    $request->request->getString('code'),
                    $request->request->getString('redirect_uri'),
                    $request->request->getString('code_verifier'),
                ),
                'refresh_token' => $this->refreshTokenHandler->handle(
                    $request->request->getString('client_id'),
                    $request->request->getString('refresh_token'),
                ),
                default => throw new OAuthException('unsupported_grant_type', 'Grant type not supported'),
            };
        } catch (OAuthException $e) {
            return $this->errorResponse($e->getError(), $e->getDescription());
        }

        $response = $this->tokenResponse($pair);
        $response->headers->set('Cache-Control', 'no-store');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
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

    private function errorResponse(string $error, string $description): JsonResponse
    {
        return new JsonResponse(['error' => $error, 'error_description' => $description], Response::HTTP_BAD_REQUEST);
    }
}

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

use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthClient;
use Sylius\AdminMcpServerPlugin\OAuth\Exception\OAuthException;
use Sylius\AdminMcpServerPlugin\OAuth\Registration\ClientRegistrar;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class RegistrationController
{
    public function __construct(
        private ClientRegistrar $clientRegistrar,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($request->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $this->error('invalid_client_metadata', 'Invalid JSON body');
        }

        try {
            [$client, $plainSecret] = $this->clientRegistrar->register($data);
        } catch (OAuthException $e) {
            return $this->error($e->getError(), $e->getDescription());
        }

        return new JsonResponse($this->buildResponse($client, $plainSecret), Response::HTTP_CREATED);
    }

    /** @return array<string, mixed> */
    private function buildResponse(OAuthClient $client, ?string $plainSecret): array
    {
        $response = [
            'client_id' => $client->getClientId(),
            'client_id_issued_at' => $client->getCreatedAt()->getTimestamp(),
            'redirect_uris' => $client->getRedirectUris(),
            'grant_types' => $client->getGrantTypes(),
            'token_endpoint_auth_method' => $client->getTokenEndpointAuthMethod(),
            'client_name' => $client->getClientName(),
        ];

        if ($plainSecret !== null) {
            $response['client_secret'] = $plainSecret;
            $response['client_secret_expires_at'] = 0;
        }

        return $response;
    }

    private function error(string $error, string $description): JsonResponse
    {
        return new JsonResponse(['error' => $error, 'error_description' => $description], Response::HTTP_BAD_REQUEST);
    }
}

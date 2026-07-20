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
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Sylius\AdminMcpServerPlugin\Security\RedirectUriValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class RegistrationController
{
    public function __construct(
        private OAuthClientRepository $clientRepository,
        private RedirectUriValidator $redirectUriValidator,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $data */
        $data = json_decode($request->getContent(), true, flags: \JSON_THROW_ON_ERROR);

        $redirectUris = $data['redirect_uris'] ?? null;
        if (!\is_array($redirectUris) || $redirectUris === []) {
            return $this->error('invalid_client_metadata', 'redirect_uris is required');
        }

        foreach ($redirectUris as $uri) {
            if (!$this->redirectUriValidator->isValid($uri)) {
                return $this->error('invalid_redirect_uri', 'All redirect_uris must use HTTPS (or localhost for development)');
            }
        }

        $clientName = \is_string($data['client_name'] ?? null) ? $data['client_name'] : 'MCP Client';
        $authMethod = \is_string($data['token_endpoint_auth_method'] ?? null) ? $data['token_endpoint_auth_method'] : 'none';
        /** @var list<string> $grantTypes */
        $grantTypes = \is_array($data['grant_types'] ?? null) ? $data['grant_types'] : ['authorization_code', 'refresh_token'];

        $plainSecret = $authMethod !== 'none' ? bin2hex(random_bytes(32)) : null;

        $client = OAuthClient::register(
            redirectUris: $redirectUris,
            clientName: $clientName,
            tokenEndpointAuthMethod: $authMethod,
            grantTypes: $grantTypes,
            plainSecret: $plainSecret,
        );

        $this->clientRepository->save($client);

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

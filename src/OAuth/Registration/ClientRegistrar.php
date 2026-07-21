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

namespace Sylius\AdminMcpServerPlugin\OAuth\Registration;

use Sylius\AdminMcpServerPlugin\Entity\OAuth\OAuthClient;
use Sylius\AdminMcpServerPlugin\OAuth\Exception\OAuthException;
use Sylius\AdminMcpServerPlugin\Repository\OAuth\OAuthClientRepository;
use Sylius\AdminMcpServerPlugin\Security\OAuth\RedirectUriValidator;

final readonly class ClientRegistrar
{
    public function __construct(
        private OAuthClientRepository $clientRepository,
        private RedirectUriValidator $redirectUriValidator,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array{OAuthClient, ?string}
     */
    public function register(array $data): array
    {
        $redirectUris = $data['redirect_uris'] ?? null;
        if (!\is_array($redirectUris) || $redirectUris === []) {
            throw new OAuthException('invalid_client_metadata', 'redirect_uris is required');
        }

        foreach ($redirectUris as $uri) {
            if (!$this->redirectUriValidator->isValid($uri)) {
                throw new OAuthException('invalid_redirect_uri', 'All redirect_uris must use HTTPS (or localhost for development)');
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

        return [$client, $plainSecret];
    }
}

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

final readonly class ClientRegistrar
{
    public function __construct(
        private OAuthClientRepository $clientRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array{OAuthClient, ?string}
     */
    public function register(array $data): array
    {
        $rawRedirectUris = $data['redirect_uris'] ?? null;
        if (!\is_array($rawRedirectUris)) {
            throw new OAuthException('invalid_client_metadata', 'redirect_uris is required');
        }

        $redirectUris = array_values(array_filter($rawRedirectUris, 'is_string'));
        if ($redirectUris === [] || \count($redirectUris) !== \count($rawRedirectUris)) {
            throw new OAuthException('invalid_client_metadata', 'redirect_uris must be a non-empty list of strings');
        }

        $clientName = \is_string($data['client_name'] ?? null) ? $data['client_name'] : 'MCP Client';
        $authMethod = \is_string($data['token_endpoint_auth_method'] ?? null) ? $data['token_endpoint_auth_method'] : 'none';

        $grantTypes = ['authorization_code', 'refresh_token'];
        $rawGrantTypes = $data['grant_types'] ?? null;
        if (\is_array($rawGrantTypes)) {
            $grantTypes = array_values(array_filter($rawGrantTypes, 'is_string'));
            if ($grantTypes === [] || \count($grantTypes) !== \count($rawGrantTypes)) {
                throw new OAuthException('invalid_client_metadata', 'grant_types must be a non-empty list of strings');
            }
        }
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

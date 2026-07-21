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

namespace Sylius\AdminMcpServerPlugin\OAuth\Metadata;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class OAuthServerMetadataProvider
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /** @return array<string, mixed> */
    public function authorizationServer(string $baseUrl): array
    {
        return [
            'issuer' => $baseUrl,
            'authorization_endpoint' => $this->urlGenerator->generate('sylius_admin_mcp_server_oauth_authorize', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'token_endpoint' => $this->urlGenerator->generate('sylius_admin_mcp_server_oauth_token', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'registration_endpoint' => $this->urlGenerator->generate('sylius_admin_mcp_server_oauth_register', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'scopes_supported' => ['mcp'],
            'response_types_supported' => ['code'],
            'grant_types_supported' => ['authorization_code', 'refresh_token'],
            'token_endpoint_auth_methods_supported' => ['client_secret_post', 'none'],
            'code_challenge_methods_supported' => ['S256'],
        ];
    }

    /** @return array<string, mixed> */
    public function protectedResource(string $baseUrl): array
    {
        return [
            'resource' => $baseUrl . '/_mcp',
            'authorization_servers' => [$baseUrl],
            'scopes_supported' => ['mcp'],
            'bearer_methods_supported' => ['header'],
        ];
    }
}

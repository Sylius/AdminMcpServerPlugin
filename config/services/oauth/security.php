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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminMcpServerPlugin\Security\Mcp\McpAccessTokenHandler;
use Sylius\AdminMcpServerPlugin\Security\Mcp\McpBearerAuthListener;
use Sylius\AdminMcpServerPlugin\Security\OAuth\PkceVerifier;
use Sylius\AdminMcpServerPlugin\Security\OAuth\RedirectUriValidator;
use Sylius\AdminMcpServerPlugin\Security\OAuth\TokenHasher;
use Symfony\Component\HttpKernel\KernelEvents;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.security.oauth.token_hasher', TokenHasher::class);

    $services->set('sylius_admin_mcp_server.security.oauth.pkce_verifier', PkceVerifier::class);

    $services->set('sylius_admin_mcp_server.security.oauth.redirect_uri_validator', RedirectUriValidator::class);

    $services->set('sylius_admin_mcp_server.security.mcp_access_token_handler', McpAccessTokenHandler::class)
        ->args([
            service('sylius_admin_mcp_server.repository.oauth.access_token'),
            service('sylius_admin_mcp_server.security.oauth.token_hasher'),
        ]);

    $services->set('sylius_admin_mcp_server.security.mcp.bearer_auth_listener', McpBearerAuthListener::class)
        ->args([
            service('sylius_admin_mcp_server.repository.oauth.access_token'),
            service('sylius_admin_mcp_server.security.oauth.token_hasher'),
        ])
        ->tag('kernel.event_listener', ['event' => KernelEvents::REQUEST, 'method' => '__invoke', 'priority' => 10]);
};

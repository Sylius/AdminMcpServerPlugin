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

use Sylius\AdminMcpServerPlugin\Provider\CredentialsTokenProvider;
use Sylius\AdminMcpServerPlugin\Provider\OAuthJwtTokenProvider;
use Sylius\AdminMcpServerPlugin\Provider\TokenProviderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.provider.token.credentials', CredentialsTokenProvider::class)
        ->args([
            service('sylius_admin_mcp_server.token_storage'),
            service('sylius_admin_mcp_server.authenticator'),
            param('sylius_admin_mcp_server.api.email'),
            param('sylius_admin_mcp_server.api.password'),
        ]);

    $services->set('sylius_admin_mcp_server.provider.token.oauth', OAuthJwtTokenProvider::class)
        ->args([
            service('lexik_jwt_authentication.jwt_manager'),
            service('request_stack'),
            service('sylius_admin_mcp_server.token_storage'),
        ]);

    $services->alias('sylius_admin_mcp_server.provider.token', 'sylius_admin_mcp_server.provider.token.oauth');
    $services->alias(TokenProviderInterface::class, 'sylius_admin_mcp_server.provider.token');
};

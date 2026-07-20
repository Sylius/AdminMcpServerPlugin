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

use Sylius\AdminMcpServerPlugin\Provider\CredentialsTokenProvider;
use Sylius\AdminMcpServerPlugin\Provider\SessionTokenProvider;
use Sylius\AdminMcpServerPlugin\Provider\TokenProviderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.provider.token.session', SessionTokenProvider::class)
        ->args([service('sylius_admin_mcp_server.token_storage')]);

    $services->set('sylius_admin_mcp_server.provider.token.credentials', CredentialsTokenProvider::class)
        ->args([
            service('sylius_admin_mcp_server.token_storage'),
            service('sylius_admin_mcp_server.authenticator'),
            param('sylius_admin_mcp_server.api.email'),
            param('sylius_admin_mcp_server.api.password'),
        ]);

    $services->alias('sylius_admin_mcp_server.provider.token', 'sylius_admin_mcp_server.provider.token.session');
    $services->alias(TokenProviderInterface::class, 'sylius_admin_mcp_server.provider.token');
};

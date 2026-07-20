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

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Sylius\AdminMcpServerPlugin\Api\AuthenticatorInterface;
use Sylius\AdminMcpServerPlugin\Api\HttpApiClient;
use Sylius\AdminMcpServerPlugin\Api\HttpAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.authenticator', HttpAuthenticator::class)
        ->args([service('sylius_admin_mcp_server.http_client.api')]);

    $services->alias(AuthenticatorInterface::class, 'sylius_admin_mcp_server.authenticator');

    $services->set('sylius_admin_mcp_server.client.api', HttpApiClient::class)
        ->args([
            service('sylius_admin_mcp_server.http_client.api'),
            service('sylius_admin_mcp_server.http_client.api_merge_patch'),
            service('sylius_admin_mcp_server.provider.token'),
        ]);

    $services->alias(ApiClientInterface::class, 'sylius_admin_mcp_server.client.api');
};

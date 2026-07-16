<?php

declare(strict_types=1);

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Sylius\AdminMcpServerPlugin\Api\HttpApiClient;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.client.api', HttpApiClient::class)
        ->args([
            service('sylius_admin_mcp_server.http_client.api'),
            service('sylius_admin_mcp_server.http_client.api_merge_patch'),
            service('sylius_admin_mcp_server.provider.token'),
        ]);

    $services->alias(ApiClientInterface::class, 'sylius_admin_mcp_server.client.api');
};

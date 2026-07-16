<?php

declare(strict_types=1);

use Sylius\AdminMcpServerPlugin\Provider\RequestTokenProvider;
use Sylius\AdminMcpServerPlugin\Provider\TokenProviderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.provider.token', RequestTokenProvider::class)
        ->args([service('request_stack')]);

    $services->alias(TokenProviderInterface::class, 'sylius_admin_mcp_server.provider.token');
};

<?php

declare(strict_types=1);

use Acme\SyliusExamplePlugin\Session\SessionTokenStorage;
use Acme\SyliusExamplePlugin\Session\TokenStorageInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.token_storage', SessionTokenStorage::class)
        ->args([service('sylius_admin_mcp_server.mcp.current_session')]);

    $services->alias(TokenStorageInterface::class, 'sylius_admin_mcp_server.token_storage');
};

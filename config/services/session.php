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

use Sylius\AdminMcpServerPlugin\Session\SessionTokenStorage;
use Sylius\AdminMcpServerPlugin\Session\TokenStorageInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.token_storage', SessionTokenStorage::class)
        ->args([service('sylius_admin_mcp_server.mcp.current_session')]);

    $services->alias(TokenStorageInterface::class, 'sylius_admin_mcp_server.token_storage');
};

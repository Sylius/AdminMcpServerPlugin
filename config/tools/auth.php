<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminMcpServerPlugin\Tool\Auth\Login;
use Sylius\AdminMcpServerPlugin\Tool\Auth\Logout;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(Login::class)
        ->args([
            service('sylius_admin_mcp_server.authenticator'),
            service('sylius_admin_mcp_server.token_storage'),
        ])
        ->tag('mcp.tool');

    $services->set(Logout::class)
        ->args([service('sylius_admin_mcp_server.token_storage')])
        ->tag('mcp.tool');
};

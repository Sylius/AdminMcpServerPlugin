<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->alias('sylius_admin_mcp_server.provider.token', 'sylius_admin_mcp_server.provider.token.credentials');
};

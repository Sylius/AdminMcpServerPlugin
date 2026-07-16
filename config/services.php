<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminMcpServerPlugin\Loader\PluginDiscoveryLoader;

return static function (ContainerConfigurator $container): void {
    $container->import('services/**');
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.mcp.loader.plugin_discovery', PluginDiscoveryLoader::class)
        ->args([service('logger')])
        ->tag('monolog.logger', ['channel' => 'mcp'])
        ->tag('mcp.loader');
};

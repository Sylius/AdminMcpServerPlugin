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

use Mcp\Event\RequestEvent;
use Sylius\AdminMcpServerPlugin\EventListener\BindSessionListener;
use Sylius\AdminMcpServerPlugin\Loader\PluginDiscoveryLoader;
use Sylius\AdminMcpServerPlugin\Session\CurrentSession;

return static function (ContainerConfigurator $container): void {
    $container->import('services/**');
    $services = $container->services();

    $services->set('sylius_admin_mcp_server.mcp.loader.plugin_discovery', PluginDiscoveryLoader::class)
        ->args([service('logger')])
        ->tag('monolog.logger', ['channel' => 'mcp'])
        ->tag('mcp.loader');

    $services->set('sylius_admin_mcp_server.mcp.current_session', CurrentSession::class);

    $services->set('sylius_admin_mcp_server.mcp.event_listener.bind_session', BindSessionListener::class)
        ->args([service('sylius_admin_mcp_server.mcp.current_session')])
        ->tag('kernel.event_listener', ['event' => RequestEvent::class, 'method' => '__invoke']);
};

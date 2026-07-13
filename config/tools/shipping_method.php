<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Index;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Show;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Create;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Update;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Delete;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Archive;
use Sylius\AdminMcpServerPlugin\Tool\ShippingMethod\Restore;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $api = service('sylius_admin_mcp_server.client.api');

    $services->set(Index::class)->args([$api])->tag('mcp.tool');
    $services->set(Show::class)->args([$api])->tag('mcp.tool');
    $services->set(Create::class)->args([$api])->tag('mcp.tool');
    $services->set(Update::class)->args([$api])->tag('mcp.tool');
    $services->set(Delete::class)->args([$api])->tag('mcp.tool');
    $services->set(Archive::class)->args([$api])->tag('mcp.tool');
    $services->set(Restore::class)->args([$api])->tag('mcp.tool');
};

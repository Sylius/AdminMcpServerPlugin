<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminMcpServerPlugin\Tool\ProductAttribute\Index;
use Sylius\AdminMcpServerPlugin\Tool\ProductAttribute\Show;
use Sylius\AdminMcpServerPlugin\Tool\ProductAttribute\Create;
use Sylius\AdminMcpServerPlugin\Tool\ProductAttribute\Update;
use Sylius\AdminMcpServerPlugin\Tool\ProductAttribute\SetValue;
use Sylius\AdminMcpServerPlugin\Tool\ProductAttribute\RemoveValue;
use Sylius\AdminMcpServerPlugin\Tool\ProductAttribute\Delete;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $api = service('sylius_admin_mcp_server.client.api');

    $services->set(Index::class)->args([$api])->tag('mcp.tool');
    $services->set(Show::class)->args([$api])->tag('mcp.tool');
    $services->set(Create::class)->args([$api])->tag('mcp.tool');
    $services->set(Update::class)->args([$api])->tag('mcp.tool');
    $services->set(SetValue::class)->args([$api])->tag('mcp.tool');
    $services->set(RemoveValue::class)->args([$api])->tag('mcp.tool');
    $services->set(Delete::class)->args([$api])->tag('mcp.tool');
};

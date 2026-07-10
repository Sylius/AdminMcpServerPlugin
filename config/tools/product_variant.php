<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminMcpServerPlugin\Tool\ProductVariant\Index;
use Sylius\AdminMcpServerPlugin\Tool\ProductVariant\Show;
use Sylius\AdminMcpServerPlugin\Tool\ProductVariant\Create;
use Sylius\AdminMcpServerPlugin\Tool\ProductVariant\Update;
use Sylius\AdminMcpServerPlugin\Tool\ProductVariant\Delete;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $api = service('sylius_admin_mcp_server.client.api');

    $services->set(Index::class)->args([$api])->tag('mcp.tool');
    $services->set(Show::class)->args([$api])->tag('mcp.tool');
    $services->set(Create::class)->args([$api])->tag('mcp.tool');
    $services->set(Update::class)->args([$api])->tag('mcp.tool');
    $services->set(Delete::class)->args([$api])->tag('mcp.tool');
};

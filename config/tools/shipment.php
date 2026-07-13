<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminMcpServerPlugin\Tool\Shipment\Index;
use Sylius\AdminMcpServerPlugin\Tool\Shipment\Show;
use Sylius\AdminMcpServerPlugin\Tool\Shipment\ResendEmail;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $api = service('sylius_admin_mcp_server.client.api');

    $services->set(Index::class)->args([$api])->tag('mcp.tool');
    $services->set(Show::class)->args([$api])->tag('mcp.tool');
    $services->set(ResendEmail::class)->args([$api])->tag('mcp.tool');
};

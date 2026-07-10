<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Index;
use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Show;
use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Update;
use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Accept;
use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Reject;
use Sylius\AdminMcpServerPlugin\Tool\ProductReview\Delete;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $api = service('sylius_admin_mcp_server.client.api');

    $services->set(Index::class)->args([$api])->tag('mcp.tool');
    $services->set(Show::class)->args([$api])->tag('mcp.tool');
    $services->set(Update::class)->args([$api])->tag('mcp.tool');
    $services->set(Accept::class)->args([$api])->tag('mcp.tool');
    $services->set(Reject::class)->args([$api])->tag('mcp.tool');
    $services->set(Delete::class)->args([$api])->tag('mcp.tool');
};

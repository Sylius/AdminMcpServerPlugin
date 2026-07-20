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

use Sylius\AdminMcpServerPlugin\Tool\Order\Cancel;
use Sylius\AdminMcpServerPlugin\Tool\Order\CompletePayment;
use Sylius\AdminMcpServerPlugin\Tool\Order\GetItem;
use Sylius\AdminMcpServerPlugin\Tool\Order\Index;
use Sylius\AdminMcpServerPlugin\Tool\Order\ListItems;
use Sylius\AdminMcpServerPlugin\Tool\Order\ListPayments;
use Sylius\AdminMcpServerPlugin\Tool\Order\ListShipments;
use Sylius\AdminMcpServerPlugin\Tool\Order\RefundPayment;
use Sylius\AdminMcpServerPlugin\Tool\Order\ResendConfirmation;
use Sylius\AdminMcpServerPlugin\Tool\Order\ShipShipment;
use Sylius\AdminMcpServerPlugin\Tool\Order\Show;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $api = service('sylius_admin_mcp_server.client.api');

    $services->set(Index::class)->args([$api])->tag('mcp.tool');
    $services->set(Show::class)->args([$api])->tag('mcp.tool');
    $services->set(Cancel::class)->args([$api])->tag('mcp.tool');
    $services->set(ShipShipment::class)->args([$api])->tag('mcp.tool');
    $services->set(CompletePayment::class)->args([$api])->tag('mcp.tool');
    $services->set(RefundPayment::class)->args([$api])->tag('mcp.tool');
    $services->set(ResendConfirmation::class)->args([$api])->tag('mcp.tool');
    $services->set(GetItem::class)->args([$api])->tag('mcp.tool');
    $services->set(ListItems::class)->args([$api])->tag('mcp.tool');
    $services->set(ListShipments::class)->args([$api])->tag('mcp.tool');
    $services->set(ListPayments::class)->args([$api])->tag('mcp.tool');
};

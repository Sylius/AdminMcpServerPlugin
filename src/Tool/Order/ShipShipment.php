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

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'ship_shipment',
    description: 'ship_shipment(shipmentId, trackingCode?) → Marks a shipment as "shipped" and optionally records the carrier tracking number. The shipment must be in "ready" state. shipmentId is the numeric ID — get it from list_order_shipments(orderToken) or list_shipments(state="ready"). trackingCode is optional (e.g. "1Z999AA10123456784"). Returns empty string on success.',
)]
final readonly class ShipShipment
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(int $shipmentId, string $trackingCode = ''): string
    {
        $body = [];
        if ($trackingCode !== '') {
            $body['trackingCode'] = $trackingCode;
        }

        return $this->client->patch(sprintf('shipments/%d/ship', $shipmentId), $body);
    }
}

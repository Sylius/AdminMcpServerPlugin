<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'ship_shipment',
    description: 'ship_shipment(shipmentId, trackingCode?) → Marks a shipment as shipped. The shipment must be in "ready" state. shipmentId is the numeric ID from list_order_shipments or list_shipments. Returns empty string on success (HTTP 202).',
)]
final readonly class ShipShipment
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int    $shipmentId   Numeric shipment ID (from list_order_shipments or list_shipments).
     * @param string $trackingCode Optional carrier tracking code. Default = "".
     */
    public function __invoke(int $shipmentId, string $trackingCode = ''): string
    {
        $body = [];
        if ($trackingCode !== '') {
            $body['tracking'] = $trackingCode;
        }

        return $this->client->patch(
            sprintf('shipments/%d/ship', $shipmentId),
            $body,
        );
    }
}

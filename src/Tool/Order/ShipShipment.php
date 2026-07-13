<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'ship_order',
    description: 'ship_order(tokenValue, shipmentId, trackingCode?) → Marks an order shipment as shipped. The shipment must be in "ready" state. shipmentId is the numeric ID from get_order shipments array. Returns JSON of the updated shipment.',
)]
final readonly class ShipShipment
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $tokenValue   Order token value.
     * @param int    $shipmentId   Numeric shipment ID (from get_order response shipments[].id).
     * @param string $trackingCode Optional carrier tracking code. Default = "".
     */
    public function __invoke(string $tokenValue, int $shipmentId, string $trackingCode = ''): string
    {
        $body = [];
        if ($trackingCode !== '') {
            $body['tracking'] = $trackingCode;
        }

        return $this->client->patch(
            sprintf('orders/%s/shipments/%d/ship', $tokenValue, $shipmentId),
            $body,
        );
    }
}

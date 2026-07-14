<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_order_shipments',
    description: 'list_order_shipments(tokenValue) → JSON collection of shipments for a Sylius order. Each shipment has: id, state (ready=waiting to ship / shipped=already sent / cancelled), method (carrier), tracking (tracking number if any), createdAt. Use ship_shipment(shipmentId) to mark a ready shipment as shipped.',
)]
final readonly class ListShipments
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $tokenValue Order token value.
     */
    public function __invoke(string $tokenValue): string
    {
        return $this->client->get(sprintf('orders/%s/shipments', $tokenValue));
    }
}

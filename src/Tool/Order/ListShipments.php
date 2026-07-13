<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_order_shipments',
    description: 'list_order_shipments(tokenValue) → JSON collection of shipments for a Sylius order. Each shipment has: id (use for ship_order), state (ready/shipped/cancelled), method, tracking, createdAt. Use ship_order(tokenValue, id) to mark as shipped.',
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

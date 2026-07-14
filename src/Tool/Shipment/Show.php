<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Shipment;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_shipment',
    description: 'get_shipment(id) → Gets full details of a shipment by its numeric ID. Returns: id, state (ready/shipped/cancelled), method (shipping carrier), order (which order), units (items in this shipment), tracking (tracking number), shippedAt, createdAt. Get the ID from list_shipments or list_order_shipments.',
)]
final readonly class Show
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('shipments/%d', $id));
    }
}

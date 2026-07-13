<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Shipment;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_shipment',
    description: 'get_shipment(id) → Full JSON of a single shipment by its numeric ID. Returns: id, state (ready/shipped/cancelled), method, order, units, tracking, shippedAt, createdAt, updatedAt.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Numeric shipment ID.
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('shipments/%d', $id));
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_order_item',
    description: 'get_order_item(id) → Full JSON of a single order item by its numeric ID. Returns: id, productName, variantName, variant, quantity, unitPrice, total, units, adjustments.',
)]
final readonly class GetItem
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Numeric order item ID.
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('order-items/%d', $id));
    }
}

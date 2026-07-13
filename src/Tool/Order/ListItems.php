<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_order_items',
    description: 'list_order_items(tokenValue) → JSON array of items for a Sylius order. Each item has: id, quantity, unitPrice, total, productName, variantName, units.',
)]
final readonly class ListItems
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
        $order = json_decode($this->client->get(sprintf('orders/%s', $tokenValue)), true);

        return (string) json_encode($order['items'] ?? []);
    }
}

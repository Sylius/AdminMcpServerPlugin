<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_order',
    description: 'get_order(tokenValue) → Full JSON object of a single Sylius order. Returns all details: tokenValue, number, state, paymentState, shippingState, customer, channel, billingAddress, shippingAddress, items (product, quantity, unitPrice, total), shipments (id, state, method), payments (id, state, method, amount), adjustments, total, itemsTotal, shippingTotal, taxTotal, createdAt, updatedAt.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $tokenValue Order token value (e.g. "xh3n2a8f1k"). Use list_orders to find it.
     */
    public function __invoke(string $tokenValue): string
    {
        return $this->client->get(sprintf('orders/%s', $tokenValue));
    }
}

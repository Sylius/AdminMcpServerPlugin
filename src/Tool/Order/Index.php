<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_orders',
    description: <<<'DESC'
list_orders(page?, itemsPerPage?, customerId?, channelCode?) → Lists orders sorted newest-first. Each order has: tokenValue (needed for all other order operations), number (like "000000012"), state (new/fulfilled/cancelled), paymentState (new/awaiting_payment/paid/refunded), shippingState (ready/shipped/cancelled), checkoutCompletedAt, total (in smallest currency unit).

Filter by customerId (numeric — from list_customers) to see all orders for a specific customer. Filter by channelCode to see orders from a specific sales channel.

NOTE: Filtering by order number or payment/shipping state is not supported by the API — browse pages or filter by customerId instead. Use get_order(tokenValue) for full details including items, payments and shipments.
DESC,
)]
final readonly class Index
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(
        int $page = 1,
        int $itemsPerPage = 30,
        int $customerId = 0,
        string $channelCode = '',
    ): string {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        if ($customerId > 0) { $params['customer.id'] = $customerId; }
        if ($channelCode !== '') { $params['channel.code'] = $channelCode; }
        return $this->client->get('orders', $params);
    }
}

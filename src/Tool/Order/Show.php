<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_order',
    description: 'get_order(tokenValue) → Gets full details of an order. Returns: tokenValue (the order\'s unique ID), number (human-readable like "#000000012"), state (new/cart/addressed/payment_selected/shipping_selected/shipped/fulfilled/cancelled), paymentState (awaiting_payment/paid/partially_refunded/refunded), shippingState, customer info, billing/shipping address, items (products ordered with quantities and prices), shipments (with IDs for ship_shipment), payments (with IDs for complete_payment/refund_payment), totals. Get tokenValue from list_orders.',
)]
final readonly class Show
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(string $tokenValue): string
    {
        return $this->client->get(sprintf('orders/%s', $tokenValue));
    }
}

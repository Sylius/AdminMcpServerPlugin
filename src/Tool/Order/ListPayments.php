<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_order_payments',
    description: 'list_order_payments(tokenValue) → JSON collection of payments for a Sylius order. Each payment has: id (use for complete_payment/refund_payment), state (new/processing/completed/failed/cancelled/refunded), method, amount, currencyCode. Use complete_payment or refund_payment to change state.',
)]
final readonly class ListPayments
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
        return $this->client->get(sprintf('orders/%s/payments', $tokenValue));
    }
}

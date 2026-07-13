<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'refund_payment',
    description: 'refund_payment(tokenValue, paymentId) → Refunds an order payment. paymentId is the numeric ID from get_order payments array. The payment must be in "completed" state. Returns JSON of the updated payment.',
)]
final readonly class RefundPayment
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $tokenValue Order token value.
     * @param int    $paymentId  Numeric payment ID (from get_order response payments[].id).
     */
    public function __invoke(string $tokenValue, int $paymentId): string
    {
        return $this->client->patch(
            sprintf('orders/%s/payments/%d/refund', $tokenValue, $paymentId),
            [],
        );
    }
}

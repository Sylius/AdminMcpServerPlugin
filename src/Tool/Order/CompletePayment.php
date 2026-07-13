<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'complete_payment',
    description: 'complete_payment(paymentId) → Marks a payment as completed (paid). paymentId is the numeric ID from list_order_payments or list_payments. The payment must be in "new" or "processing" state. Returns JSON of the updated payment.',
)]
final readonly class CompletePayment
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $paymentId Numeric payment ID (from list_order_payments or list_payments).
     */
    public function __invoke(int $paymentId): string
    {
        return $this->client->patch(
            sprintf('payments/%d/complete', $paymentId),
            [],
        );
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'complete_payment',
    description: 'complete_payment(tokenValue, paymentId) → Marks an order payment as completed (paid). paymentId is the numeric ID from get_order payments array. The payment must be in "new" or "processing" state. Returns JSON of the updated payment.',
)]
final readonly class CompletePayment
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
            sprintf('orders/%s/payments/%d/complete', $tokenValue, $paymentId),
            [],
        );
    }
}

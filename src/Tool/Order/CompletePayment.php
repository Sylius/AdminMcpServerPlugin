<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'complete_payment',
    description: 'complete_payment(paymentId) → Marks a payment as completed (paid). The payment must be in "new" or "processing" state. paymentId is the numeric ID — get it from list_order_payments(orderToken) or list_payments(). Use this to manually confirm a payment (e.g. bank transfer received). Returns JSON of the updated payment.',
)]
final readonly class CompletePayment
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(int $paymentId): string
    {
        return $this->client->patch(sprintf('payments/%d/complete', $paymentId), []);
    }
}

<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'refund_payment',
    description: 'refund_payment(paymentId) → Refunds a payment back to the customer. The payment must be in "completed" state. paymentId is the numeric ID — get it from list_order_payments(orderToken) or list_payments(state="completed"). Returns JSON of the updated payment (state will become "refunded").',
)]
final readonly class RefundPayment
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(int $paymentId): string
    {
        return $this->client->patch(sprintf('payments/%d/refund', $paymentId), []);
    }
}

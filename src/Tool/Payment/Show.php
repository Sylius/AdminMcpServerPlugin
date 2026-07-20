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

namespace Sylius\AdminMcpServerPlugin\Tool\Payment;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_payment',
    description: 'get_payment(paymentId) → Gets full details of a payment by its numeric ID. Returns: id, state (new/processing/completed/failed/cancelled/refunded), method (payment gateway used), amount (in smallest currency unit, e.g. 1000 = 10.00 EUR), currencyCode, order. Get the ID from list_payments or list_order_payments.',
)]
final readonly class Show
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    /**
     * @param int $paymentId Numeric payment ID (from list_payments or list_order_payments).
     */
    public function __invoke(int $paymentId): string
    {
        return $this->client->get(sprintf('payments/%d', $paymentId));
    }
}

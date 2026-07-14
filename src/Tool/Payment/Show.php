<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Payment;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_payment',
    description: 'get_payment(id) → Gets full details of a payment by its numeric ID. Returns: id, state (new/processing/completed/failed/cancelled/refunded), method (payment gateway used), amount (in smallest currency unit, e.g. 1000 = 10.00 EUR), currencyCode, order. Get the ID from list_payments or list_order_payments.',
)]
final readonly class Show
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('payments/%d', $id));
    }
}

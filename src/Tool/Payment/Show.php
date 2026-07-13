<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Payment;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_payment',
    description: 'get_payment(id) → Full JSON of a single payment by its numeric ID. Returns: id, state (new/processing/completed/failed/cancelled/refunded), method, amount, currencyCode, order, details.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Numeric payment ID.
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('payments/%d', $id));
    }
}

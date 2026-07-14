<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ExchangeRate;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_exchange_rate',
    description: 'delete_exchange_rate(id) → empty string on success (HTTP 204). Permanently deletes the Sylius exchange rate with the given numeric ID. Use list_exchange_rates to find the ID.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Numeric exchange rate ID to delete.
     */
    public function __invoke(int $id): string
    {
        return $this->client->delete(sprintf('exchange-rates/%d', $id));
    }
}

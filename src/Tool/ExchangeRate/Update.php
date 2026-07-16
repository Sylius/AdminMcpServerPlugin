<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ExchangeRate;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_exchange_rate',
    description: 'update_exchange_rate(id, body) → JSON of the updated exchange rate. id is numeric (from list_exchange_rates). body (JSON string) — fields: ratio (float). Example: \'{"ratio": 0.95}\'',
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(int $id, string $body): string
    {
        return $this->client->put(sprintf('exchange-rates/%d', $id), json_decode($body, true) ?? []);
    }
}

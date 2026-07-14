<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ExchangeRate;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_exchange_rate',
    description: 'get_exchange_rate(id) → JSON object of a single exchange rate. Returns: id, ratio, sourceCurrency (IRI), targetCurrency (IRI). Use list_exchange_rates to find the numeric id.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Numeric exchange rate ID (from list_exchange_rates).
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('exchange-rates/%d', $id));
    }
}

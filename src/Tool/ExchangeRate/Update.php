<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ExchangeRate;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_exchange_rate',
    description: 'update_exchange_rate(id, ratio) → JSON object of the updated Sylius exchange rate. Uses PUT. id is the numeric exchange rate ID (get it from list_exchange_rates).',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int   $id    Numeric exchange rate ID.
     * @param float $ratio New exchange ratio (e.g. 0.95).
     */
    public function __invoke(int $id, float $ratio): string
    {
        return $this->client->put(sprintf('exchange-rates/%d', $id), [
            'ratio' => $ratio,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ExchangeRate;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_exchange_rate',
    description: 'create_exchange_rate(sourceCurrencyCode, targetCurrencyCode, ratio) → JSON object of the newly created Sylius exchange rate. ratio is a float, e.g. 1.25 means 1 source = 1.25 target.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $sourceCurrencyCode Source currency ISO 4217 code (e.g. "USD").
     * @param string $targetCurrencyCode Target currency ISO 4217 code (e.g. "EUR").
     * @param float  $ratio              Exchange ratio (e.g. 0.92 means 1 USD = 0.92 EUR).
     */
    public function __invoke(
        string $sourceCurrencyCode,
        string $targetCurrencyCode,
        float $ratio,
    ): string {
        return $this->client->post('exchange-rates', [
            'sourceCurrency' => $this->client->iri(sprintf('currencies/%s', $sourceCurrencyCode)),
            'targetCurrency' => $this->client->iri(sprintf('currencies/%s', $targetCurrencyCode)),
            'ratio' => $ratio,
        ]);
    }
}

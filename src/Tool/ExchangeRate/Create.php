<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ExchangeRate;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_exchange_rate',
    description: 'create_exchange_rate(sourceCurrency, targetCurrency, ratio) → JSON object of the newly created Sylius exchange rate. ratio is a float, e.g. 1.25 means 1 source = 1.25 target. sourceCurrency and targetCurrency are IRIs from list_currencies @id.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $sourceCurrency Source currency IRI from list_currencies @id.
     * @param string $targetCurrency Target currency IRI from list_currencies @id.
     * @param float  $ratio          Exchange ratio (e.g. 0.92 means 1 source = 0.92 target).
     */
    public function __invoke(
        string $sourceCurrency,
        string $targetCurrency,
        float $ratio,
    ): string {
        return $this->client->post('exchange-rates', [
            'sourceCurrency' => $sourceCurrency,
            'targetCurrency' => $targetCurrency,
            'ratio'          => $ratio,
        ]);
    }
}

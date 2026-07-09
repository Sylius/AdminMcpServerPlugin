<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Currency;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_currency',
    description: 'create_currency(code) → JSON object of the newly created Sylius currency. code must be a valid ISO 4217 currency code (e.g. "USD", "EUR", "PLN", "GBP").',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code ISO 4217 currency code (e.g. "USD", "EUR", "PLN").
     */
    public function __invoke(string $code): string
    {
        return $this->client->post('currencies', ['code' => $code]);
    }
}

<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\TaxRate;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_tax_rate',
    description: 'get_tax_rate(code) → JSON object of a single Sylius tax rate. Returns: id, code, name, amount, includedInPrice, calculator, category, zone, startDate, endDate.',
)]
final readonly class Show
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code Tax rate code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('tax-rates/%s', $code));
    }
}

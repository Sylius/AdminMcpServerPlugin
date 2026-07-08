<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\TaxRate;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_tax_rate',
    description: 'create_tax_rate(code, name, amount, categoryCode, zoneCode, includedInPrice?, calculator?) → JSON object of the newly created Sylius tax rate. amount is a float (e.g. 0.23 = 23%). calculator defaults to "default".',
)]
final readonly class Create
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code             Unique tax rate code (e.g. "vat_23").
     * @param string $name             Display name (e.g. "VAT 23%").
     * @param float  $amount           Tax rate as a decimal (e.g. 0.23 for 23%).
     * @param string $categoryCode     Tax category code (e.g. "clothing").
     * @param string $zoneCode         Zone code the rate applies to (e.g. "US", "EU").
     * @param bool   $includedInPrice  Whether the tax is included in the displayed price. Default = false.
     * @param string $calculator       Calculator type. Default = "default".
     */
    public function __invoke(
        string $code,
        string $name,
        float $amount,
        string $categoryCode,
        string $zoneCode,
        bool $includedInPrice = false,
        string $calculator = 'default',
    ): string {
        return $this->client->post('tax-rates', [
            'code' => $code,
            'name' => $name,
            'amount' => $amount,
            'includedInPrice' => $includedInPrice,
            'calculator' => $calculator,
            'category' => sprintf('/api/v2/admin/tax-categories/%s', $categoryCode),
            'zone' => sprintf('/api/v2/admin/zones/%s', $zoneCode),
        ]);
    }
}

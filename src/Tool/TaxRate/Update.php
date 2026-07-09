<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\TaxRate;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_tax_rate',
    description: 'update_tax_rate(code, name, amount, categoryCode, zoneCode, includedInPrice?, calculator?) → JSON object of the updated Sylius tax rate.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code            Tax rate code to update.
     * @param string $name            New display name.
     * @param float  $amount          New tax rate as a decimal (e.g. 0.23 for 23%).
     * @param string $categoryCode    Tax category code.
     * @param string $zoneCode        Zone code the rate applies to.
     * @param bool   $includedInPrice Whether the tax is included in the displayed price. Default = false.
     * @param string $calculator      Calculator type. Default = "default".
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
        return $this->client->put(sprintf('tax-rates/%s', $code), [
            'name' => $name,
            'amount' => $amount,
            'includedInPrice' => $includedInPrice,
            'calculator' => $calculator,
            'category' => sprintf('/api/v2/admin/tax-categories/%s', $categoryCode),
            'zone' => sprintf('/api/v2/admin/zones/%s', $zoneCode),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\TaxRate;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_tax_rate',
    description: 'update_tax_rate(code, name?, amount?, category?, zone?, includedInPrice?, calculator?) → JSON object of the updated Sylius tax rate. Only provided fields are changed; omitted fields keep their current values. amount is a float (e.g. 0.23 = 23%). category is the IRI from list_tax_categories @id. zone is the IRI from list_zones @id.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string     $code            Tax rate code to update.
     * @param string     $name            New display name (omit to keep current).
     * @param float|null $amount          New tax rate as decimal (e.g. 0.23 for 23%). Null = keep current.
     * @param string     $category        Tax category IRI from list_tax_categories @id (omit to keep current).
     * @param string     $zone            Zone IRI from list_zones @id (omit to keep current).
     * @param bool|null  $includedInPrice Whether the tax is included in the displayed price. Null = keep current.
     * @param string     $calculator      Calculator type (omit to keep current).
     */
    public function __invoke(
        string $code,
        string $name = '',
        ?float $amount = null,
        string $category = '',
        string $zone = '',
        ?bool $includedInPrice = null,
        string $calculator = '',
    ): string {
        $existing = json_decode($this->client->get(sprintf('tax-rates/%s', $code)), true);

        return $this->client->put(sprintf('tax-rates/%s', $code), [
            'name'            => $name !== '' ? $name : ($existing['name'] ?? $code),
            'amount'          => $amount ?? ($existing['amount'] ?? 0.0),
            'includedInPrice' => $includedInPrice ?? ($existing['includedInPrice'] ?? false),
            'calculator'      => $calculator !== '' ? $calculator : ($existing['calculator'] ?? 'default'),
            'category'        => $category !== '' ? $category : ($existing['category'] ?? ''),
            'zone'            => $zone !== '' ? $zone : ($existing['zone'] ?? ''),
        ]);
    }
}

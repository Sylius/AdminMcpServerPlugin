<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\TaxRate;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_tax_rate',
    description: 'update_tax_rate(code, name?, amount?, categoryCode?, zoneCode?, includedInPrice?, calculator?) → JSON object of the updated Sylius tax rate. Only provided fields are changed; omitted fields keep their current values. amount is a float (e.g. 0.23 = 23%).',
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
     * @param string     $categoryCode    Tax category code (omit to keep current).
     * @param string     $zoneCode        Zone code (omit to keep current).
     * @param bool|null  $includedInPrice Whether the tax is included in the displayed price. Null = keep current.
     * @param string     $calculator      Calculator type (omit to keep current).
     */
    public function __invoke(
        string $code,
        string $name = '',
        ?float $amount = null,
        string $categoryCode = '',
        string $zoneCode = '',
        ?bool $includedInPrice = null,
        string $calculator = '',
    ): string {
        $existing = json_decode($this->client->get(sprintf('tax-rates/%s', $code)), true);

        return $this->client->put(sprintf('tax-rates/%s', $code), [
            'name'            => $name !== '' ? $name : ($existing['name'] ?? $code),
            'amount'          => $amount ?? ($existing['amount'] ?? 0.0),
            'includedInPrice' => $includedInPrice ?? ($existing['includedInPrice'] ?? false),
            'calculator'      => $calculator !== '' ? $calculator : ($existing['calculator'] ?? 'default'),
            'category'        => $categoryCode !== ''
                ? $this->client->iri(sprintf('tax-categories/%s', $this->client->normalizeCode($categoryCode)))
                : ($existing['category'] ?? ''),
            'zone'            => $zoneCode !== ''
                ? $this->client->iri(sprintf('zones/%s', $this->client->normalizeCode($zoneCode)))
                : ($existing['zone'] ?? ''),
        ]);
    }
}

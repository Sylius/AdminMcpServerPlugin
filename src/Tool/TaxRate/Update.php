<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\TaxRate;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_tax_rate',
    description: <<<'DESC'
update_tax_rate(code, body) → JSON of the updated tax rate. Only fields in body are changed.

body (JSON string) — fields: name (string), amount (float, e.g. 0.23=23%), includedInPrice (bool), calculator (string, default "default"), category (IRI from list_tax_categories @id), zone (IRI from list_zones @id).
Example: '{"name":"VAT 23%","amount":0.23,"includedInPrice":false}'
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code, string $body): string
    {
        $existing = json_decode($this->client->get(sprintf('tax-rates/%s', $code)), true);
        $b = json_decode($body, true) ?? [];

        return $this->client->put(sprintf('tax-rates/%s', $code), [
            'name'            => $b['name']            ?? ($existing['name'] ?? $code),
            'amount'          => $b['amount']          ?? ($existing['amount'] ?? 0.0),
            'includedInPrice' => $b['includedInPrice'] ?? ($existing['includedInPrice'] ?? false),
            'calculator'      => $b['calculator']      ?? ($existing['calculator'] ?? 'default'),
            'category'        => $b['category']        ?? ($existing['category'] ?? ''),
            'zone'            => $b['zone']            ?? ($existing['zone'] ?? ''),
        ]);
    }
}

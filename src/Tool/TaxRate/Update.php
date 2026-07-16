<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\TaxRate;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_tax_rate',
    description: <<<'DESC'
update_tax_rate(code, body) → JSON of the updated tax rate.

IMPORTANT: First call get_tax_rate to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body.
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
        return $this->client->put(sprintf('tax-rates/%s', $code), json_decode($body, true) ?? []);
    }
}

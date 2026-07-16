<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\TaxCategory;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_tax_category',
    description: <<<'DESC'
update_tax_category(code, body) → JSON of the updated tax category.

IMPORTANT: First call get_tax_category to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body.
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
        return $this->client->put(sprintf('tax-categories/%s', $code), json_decode($body, true) ?? []);
    }
}

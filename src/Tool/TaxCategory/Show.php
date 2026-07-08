<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\TaxCategory;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_tax_category',
    description: 'get_tax_category(code) → JSON object of a single Sylius tax category. Returns: id, code, name, description, createdAt, updatedAt.',
)]
final readonly class Show
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code Tax category code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('tax-categories/%s', $code));
    }
}

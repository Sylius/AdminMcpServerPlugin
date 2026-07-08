<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductTaxon;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_product_taxon',
    description: 'delete_product_taxon(id) → Removes a product-taxon assignment by ID. Returns empty response on success (HTTP 204).',
)]
final readonly class Delete
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param int $id Product-taxon assignment ID to delete.
     */
    public function __invoke(int $id): string
    {
        return $this->client->delete(sprintf('product-taxons/%d', $id));
    }
}

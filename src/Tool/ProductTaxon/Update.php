<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductTaxon;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_taxon',
    description: 'update_product_taxon(id, body) → JSON of the updated product-taxon assignment. Updates the display position of a product within a category. id is numeric (from list_product_taxons). body (JSON string) — fields: position (int). Example: \'{"position": 3}\'',
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(int $id, string $body): string
    {
        return $this->client->put(sprintf('product-taxons/%d', $id), json_decode($body, true) ?? []);
    }
}

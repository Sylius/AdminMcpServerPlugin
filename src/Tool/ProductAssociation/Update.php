<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociation;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_association',
    description: 'update_product_association(id, body) → JSON of the updated product association. Replaces the list of associated products. id is numeric (from list_product_associations). body (JSON string) — fields: associatedProducts (array of product IRIs). Example: \'{"associatedProducts": ["/api/v2/admin/products/MUG_001"]}\'',
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(int $id, string $body): string
    {
        return $this->client->put(sprintf('product-associations/%d', $id), json_decode($body, true) ?? []);
    }
}

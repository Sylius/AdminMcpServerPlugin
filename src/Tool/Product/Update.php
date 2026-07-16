<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Product;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product',
    description: <<<'DESC'
update_product(code, body) → JSON of the updated Sylius product.

IMPORTANT: First call get_product to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body. This preserves all required fields including translation @ids.

NOTE: slug does NOT auto-update when you change the name; update it separately if needed.
NOTE: to assign/remove product categories (taxons) use create_product_taxon / delete_product_taxon instead.
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
        return $this->client->put(sprintf('products/%s', $code), json_decode($body, true) ?? []);
    }
}

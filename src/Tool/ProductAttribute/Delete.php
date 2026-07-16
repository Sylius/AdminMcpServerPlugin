<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAttribute;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_product_attribute',
    description: 'delete_product_attribute(code) → Permanently deletes a product attribute definition and all its values assigned to products. Returns empty response on success (204).',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('product-attributes/%s', $code));
    }
}

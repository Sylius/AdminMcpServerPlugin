<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductVariant;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_product_variant',
    description: 'delete_product_variant(code) → Deletes the Sylius product variant with the given code. Returns empty response on success (HTTP 204).',
)]
final readonly class Delete
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code Product variant code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('product-variants/%s', $code));
    }
}

<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Product;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_product',
    description: 'delete_product(code) → empty string on success (HTTP 204). Permanently deletes the Sylius product with the given code.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Product code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('products/%s', $code));
    }
}

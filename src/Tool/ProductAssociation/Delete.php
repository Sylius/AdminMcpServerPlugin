<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductAssociation;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_product_association',
    description: 'delete_product_association(id) → Deletes the Sylius product association with the given ID. Returns empty response on success (HTTP 204).',
)]
final readonly class Delete
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param int $id Product association ID to delete.
     */
    public function __invoke(int $id): string
    {
        return $this->client->delete(sprintf('product-associations/%d', $id));
    }
}

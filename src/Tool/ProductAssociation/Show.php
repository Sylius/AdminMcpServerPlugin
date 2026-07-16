<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociation;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_product_association',
    description: 'get_product_association(id) → JSON object of a single product association. Returns: @id, type (association type IRI), owner (product IRI), associatedProducts (list of product IRIs). Use list_product_associations to find the numeric id (last path segment of @id).',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Numeric association ID (last path segment of @id from list_product_associations).
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('product-associations/%d', $id));
    }
}

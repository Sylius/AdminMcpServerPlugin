<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociation;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_product_association',
    description: 'create_product_association(typeCode, ownerCode, associatedProductCodes) → JSON object of the newly created Sylius product association. Each owner+type pair must be unique.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $typeCode               Association type code (e.g. "similar_products").
     * @param string   $ownerCode              Owner product code.
     * @param string[] $associatedProductCodes List of product codes to associate.
     */
    public function __invoke(string $typeCode, string $ownerCode, array $associatedProductCodes): string
    {
        return $this->client->post('product-associations', [
            'type' => sprintf('/api/v2/admin/product-association-types/%s', $typeCode),
            'owner' => sprintf('/api/v2/admin/products/%s', $ownerCode),
            'associatedProducts' => array_map(
                static fn (string $code) => sprintf('/api/v2/admin/products/%s', $code),
                $associatedProductCodes,
            ),
        ]);
    }
}

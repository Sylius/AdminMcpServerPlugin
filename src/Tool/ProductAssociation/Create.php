<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociation;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_product_association',
    description: 'create_product_association(type, owner, associatedProducts) → JSON object of the newly created Sylius product association. Each owner+type pair must be unique.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $type               Association type IRI (e.g. "/api/v2/admin/product-association-types/similar_products").
     * @param string   $owner              Owner product IRI (e.g. "/api/v2/admin/products/MUG").
     * @param string[] $associatedProducts List of product IRIs to associate.
     */
    public function __invoke(string $type, string $owner, array $associatedProducts): string
    {
        $data = json_decode($this->client->post('product-associations', [
            'type' => $type,
            'owner' => $owner,
            'associatedProducts' => $associatedProducts,
        ]), true);

        if (isset($data['@id']) && preg_match('/\/(\d+)$/', $data['@id'], $m)) {
            $data['id'] = (int) $m[1];
        }

        return (string) json_encode($data);
    }
}

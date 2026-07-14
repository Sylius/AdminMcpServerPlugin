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
        $data = json_decode($this->client->post('product-associations', [
            'type' => $this->client->iri(sprintf('product-association-types/%s', $typeCode)),
            'owner' => $this->client->iri(sprintf('products/%s', $ownerCode)),
            'associatedProducts' => array_map(
                fn (string $code) => $this->client->iri(sprintf('products/%s', $code)),
                $associatedProductCodes,
            ),
        ]), true);

        if (isset($data['@id']) && preg_match('/\/(\d+)$/', $data['@id'], $m)) {
            $data['id'] = (int) $m[1];
        }

        return (string) json_encode($data);
    }
}

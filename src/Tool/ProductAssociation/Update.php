<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociation;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_association',
    description: 'update_product_association(id, associatedProductCodes) → JSON object of the updated Sylius product association. Replaces the full list of associated products.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int      $id                     Product association ID.
     * @param string[] $associatedProductCodes New list of product codes to associate (replaces existing).
     */
    public function __invoke(int $id, array $associatedProductCodes): string
    {
        return $this->client->put(sprintf('product-associations/%d', $id), [
            'associatedProducts' => array_map(
                fn (string $code) => $this->client->iri(sprintf('products/%s', $code)),
                $associatedProductCodes,
            ),
        ]);
    }
}

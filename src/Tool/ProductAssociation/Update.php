<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductAssociation;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_association',
    description: 'update_product_association(id, associatedProductCodes) → JSON object of the updated Sylius product association. Replaces the full list of associated products.',
)]
final readonly class Update
{
    public function __construct(
        private AdminApiClient $client,
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
                static fn (string $code) => sprintf('/api/v2/admin/products/%s', $code),
                $associatedProductCodes,
            ),
        ]);
    }
}

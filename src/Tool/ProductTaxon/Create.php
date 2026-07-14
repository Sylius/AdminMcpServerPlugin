<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductTaxon;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_product_taxon',
    description: 'create_product_taxon(productCode, taxonCode, position?) → Assigns a product to a category (taxon) so it appears in that section of the shop. Each product+taxon pair must be unique — assigning it twice gives a 422 error. Use list_taxons to find taxon codes. position controls display order within the category (lower = earlier). Returns the assignment object with its id (needed for delete_product_taxon).',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $productCode Product code to assign the taxon to.
     * @param string $taxonCode   Taxon code to assign.
     * @param int    $position    Display position within the taxon. Default = 0.
     */
    public function __invoke(string $productCode, string $taxonCode, int $position = 0): string
    {
        return $this->client->post('product-taxons', [
            'product' => sprintf('/api/v2/admin/products/%s', $productCode),
            'taxon' => sprintf('/api/v2/admin/taxons/%s', $taxonCode),
            'position' => $position,
        ]);
    }
}

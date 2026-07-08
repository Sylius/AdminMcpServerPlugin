<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductTaxon;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_product_taxon',
    description: 'create_product_taxon(productCode, taxonCode, position?) → JSON object of the newly created Sylius product-taxon assignment. Each product+taxon pair must be unique.',
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

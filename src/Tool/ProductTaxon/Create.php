<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductTaxon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_product_taxon',
    description: 'create_product_taxon(product, taxon, position?) → Assigns a product to a category (taxon) so it appears in that section of the shop. Each product+taxon pair must be unique — assigning it twice gives a 422 error. product is the IRI from list_products @id. taxon is the IRI from list_taxons @id. position controls display order within the category (lower = earlier). Returns the assignment object with its id (needed for delete_product_taxon).',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $product   Product IRI from list_products @id.
     * @param string $taxon     Taxon IRI from list_taxons @id.
     * @param int    $position  Display position within the taxon. Default = 0.
     */
    public function __invoke(string $product, string $taxon, int $position = 0): string
    {
        return $this->client->post('product-taxons', [
            'product' => $product,
            'taxon' => $taxon,
            'position' => $position,
        ]);
    }
}

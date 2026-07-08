<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductTaxon;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_taxon',
    description: 'update_product_taxon(id, position) → JSON object of the updated Sylius product-taxon assignment. Updates the display position.',
)]
final readonly class Update
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param int $id       Product-taxon assignment ID.
     * @param int $position New display position within the taxon.
     */
    public function __invoke(int $id, int $position): string
    {
        return $this->client->put(sprintf('product-taxons/%d', $id), [
            'position' => $position,
        ]);
    }
}

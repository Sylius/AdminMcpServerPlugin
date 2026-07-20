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
    name: 'get_product_taxon',
    description: 'get_product_taxon(id) → JSON object of a single product-taxon assignment. Returns: id, product (IRI), taxon (IRI), position. Use list_product_taxons to find the numeric id.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('product-taxons/%d', $id));
    }
}

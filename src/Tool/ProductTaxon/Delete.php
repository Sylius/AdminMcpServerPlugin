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
    name: 'delete_product_taxon',
    description: 'delete_product_taxon(id) → Removes a product-taxon assignment by ID. Returns empty response on success (HTTP 204).',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Product-taxon assignment ID to delete.
     */
    public function __invoke(int $id): string
    {
        return $this->client->delete(sprintf('product-taxons/%d', $id));
    }
}

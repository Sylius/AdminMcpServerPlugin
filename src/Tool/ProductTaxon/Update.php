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
    name: 'update_product_taxon',
    description: 'update_product_taxon(id, body) → JSON of the updated product-taxon assignment. Updates the display position of a product within a category. id is numeric (from list_product_taxons). body (JSON string) — fields: position (int). Example: \'{"position": 3}\'',
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(int $id, string $body): string
    {
        return $this->client->put(sprintf('product-taxons/%d', $id), json_decode($body, true) ?? []);
    }
}

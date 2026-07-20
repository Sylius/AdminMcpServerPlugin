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

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociation;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_product_association',
    description: 'update_product_association(id, body) → JSON of the updated product association. Replaces the list of associated products. id is numeric (from list_product_associations). body (JSON string) — fields: associatedProducts (array of product IRIs). Example: \'{"associatedProducts": ["/api/v2/admin/products/MUG_001"]}\'',
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(int $id, string $body): string
    {
        return $this->client->put(sprintf('product-associations/%d', $id), json_decode($body, true) ?? []);
    }
}

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
    name: 'create_product_association',
    description: 'create_product_association(type, owner, associatedProducts) → JSON object of the newly created Sylius product association. The numeric id can be extracted from the @id field (last path segment, e.g. /api/v2/admin/product-associations/42 → 42). Each owner+type pair must be unique.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $type               Association type IRI (e.g. "/api/v2/admin/product-association-types/similar_products").
     * @param string   $owner              Owner product IRI (e.g. "/api/v2/admin/products/MUG").
     * @param string[] $associatedProducts List of product IRIs to associate.
     */
    public function __invoke(string $type, string $owner, array $associatedProducts): string
    {
        return $this->client->post('product-associations', [
            'type' => $type,
            'owner' => $owner,
            'associatedProducts' => $associatedProducts,
        ]);
    }
}

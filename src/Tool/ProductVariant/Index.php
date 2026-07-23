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

namespace Sylius\AdminMcpServerPlugin\Tool\ProductVariant;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_product_variants',
    description: 'list_product_variants(page?, itemsPerPage?, product?, orderBy?, orderDir?) → JSON Hydra collection of Sylius product variants. Each variant has: code, product, enabled, onHand, onHold, tracked, channelPricings, optionValues, translations (name per locale). WARNING: Without the product parameter this returns ALL variants across the entire store (can be 500+). Always pass product as the product IRI to filter — construct it as "/api/v2/admin/products/PRODUCT_CODE" or use the @id from create_product/get_product response. Use orderBy/orderDir to sort (e.g. orderBy=createdAt orderDir=desc).',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(
        int $page = 1,
        int $itemsPerPage = 30,
        string $product = '',
        string $orderBy = '',
        string $orderDir = 'asc',
    ): string {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        if ($product !== '') {
            if (!str_contains($product, '/')) {
                $product = sprintf('/api/v2/admin/products/%s', $product);
            }
            $params['product'] = $product;
        }
        if ($orderBy !== '') {
            $params['order[' . $orderBy . ']'] = $orderDir;
        }

        return $this->client->get('product-variants', $params);
    }
}

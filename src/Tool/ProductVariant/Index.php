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
    description: 'list_product_variants(page?, itemsPerPage?, product?, orderBy?, orderDir?) → JSON Hydra collection of Sylius product variants. Each variant has: code, product, enabled, onHand, onHold, tracked, channelPricings, optionValues, translations (name per locale). TIP: Always pass product (IRI from list_products @id) to filter by product — without it, all variants across the entire store are returned (can be hundreds). Use orderBy/orderDir to sort (e.g. orderBy=createdAt orderDir=desc).',
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
            $params['product'] = $product;
        }
        if ($orderBy !== '') {
            $params['order[' . $orderBy . ']'] = $orderDir;
        }

        return $this->client->get('product-variants', $params);
    }
}

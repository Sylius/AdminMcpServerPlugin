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
    name: 'get_product_variant',
    description: 'get_product_variant(code) → JSON object of a single Sylius product variant. Returns: code, product, enabled, onHand, onHold, tracked, weight, width, height, depth, taxCategory, shippingCategory, channelPricings, optionValues, translations.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Product variant code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('product-variants/%s', $code));
    }
}

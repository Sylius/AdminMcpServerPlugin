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

namespace Sylius\AdminMcpServerPlugin\Tool\ProductImage;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_product_image',
    description: 'get_product_image(productCode, imageId) → JSON object of a single product image. Returns: id, type, path, owner.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $productCode Product code.
     * @param int    $imageId     Image numeric ID (from list_product_images).
     */
    public function __invoke(string $productCode, int $imageId): string
    {
        return $this->client->get(sprintf('products/%s/images/%d', $productCode, $imageId));
    }
}

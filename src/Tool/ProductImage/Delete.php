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
    name: 'delete_product_image',
    description: 'delete_product_image(productCode, imageId) → Permanently deletes a product image. Returns empty string on success (HTTP 204). Use list_product_images to find the imageId.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $productCode Product code.
     * @param int    $imageId     Image numeric ID to delete.
     */
    public function __invoke(string $productCode, int $imageId): string
    {
        return $this->client->delete(sprintf('products/%s/images/%d', $productCode, $imageId));
    }
}

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
    name: 'list_product_images',
    description: 'list_product_images(productCode) → JSON Hydra collection of images for a Sylius product. Each image has: id (use this numeric id as the imageId parameter in get_product_image, update_product_image, delete_product_image), type (e.g. "main"), path (URL path to the image file), owner.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $productCode Product code.
     */
    public function __invoke(string $productCode): string
    {
        return $this->client->get(sprintf('products/%s/images', $productCode));
    }
}

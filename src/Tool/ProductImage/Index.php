<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductImage;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_product_images',
    description: 'list_product_images(productCode) → JSON Hydra collection of images for a Sylius product. Each image has: id (use for delete_product_image), type (e.g. "main"), path (URL path to the image file), owner.',
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

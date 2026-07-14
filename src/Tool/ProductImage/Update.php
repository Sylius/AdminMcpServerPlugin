<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductImage;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_product_image',
    description: 'update_product_image(productCode, imageId, type?, productVariants?) → Updates a product image metadata. Only provided fields are changed. type is an arbitrary label (e.g. "main", "thumbnail", "banner"). productVariants links the image to specific variants by IRI (empty array = image belongs to all variants). Use list_product_images to find the imageId.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string[] $productVariants List of product variant IRIs.
     */
    public function __invoke(
        string $productCode,
        int $imageId,
        string $type = '',
        array $productVariants = [],
    ): string {
        $existing = json_decode(
            $this->client->get(sprintf('products/%s/images/%d', $productCode, $imageId)),
            true,
        );

        $body = [];

        $body['type'] = $type !== '' ? $type : ($existing['type'] ?? null);

        if ($productVariants !== []) {
            $body['productVariants'] = $productVariants;
        } else {
            $body['productVariants'] = $existing['productVariants'] ?? [];
        }

        return $this->client->put(
            sprintf('products/%s/images/%d', $productCode, $imageId),
            $body,
        );
    }
}

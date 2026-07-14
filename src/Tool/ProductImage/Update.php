<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductImage;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_product_image',
    description: 'update_product_image(productCode, imageId, type?, productVariantCodes?) → Updates a product image metadata. Only provided fields are changed. type is an arbitrary label (e.g. "main", "thumbnail", "banner"). productVariantCodes links the image to specific variants (empty array = image belongs to all variants). Use list_product_images to find the imageId.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string[] $productVariantCodes
     */
    public function __invoke(
        string $productCode,
        int $imageId,
        string $type = '',
        array $productVariantCodes = [],
    ): string {
        $existing = json_decode(
            $this->client->get(sprintf('products/%s/images/%d', $productCode, $imageId)),
            true,
        );

        $body = [];

        $body['type'] = $type !== '' ? $type : ($existing['type'] ?? null);

        if ($productVariantCodes !== []) {
            $body['productVariants'] = array_map(
                fn (string $c) => $this->client->iri(sprintf('product-variants/%s', $this->client->normalizeCode($c))),
                $productVariantCodes,
            );
        } else {
            $body['productVariants'] = $existing['productVariants'] ?? [];
        }

        return $this->client->put(
            sprintf('products/%s/images/%d', $productCode, $imageId),
            $body,
        );
    }
}

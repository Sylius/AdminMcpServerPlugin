<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductImage;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_product_image',
    description: <<<'DESC'
update_product_image(productCode, imageId, body) → JSON of the updated product image. Only fields in body are changed.

body (JSON string) — fields: type (string label, e.g. "main"/"thumbnail"/"banner"), productVariants (array of variant IRIs — empty array = image belongs to all variants).
Example: '{"type":"thumbnail"}'
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $productCode, int $imageId, string $body): string
    {
        $existing = json_decode(
            $this->client->get(sprintf('products/%s/images/%d', $productCode, $imageId)),
            true,
        );
        $b = json_decode($body, true) ?? [];

        return $this->client->put(
            sprintf('products/%s/images/%d', $productCode, $imageId),
            [
                'type'            => $b['type']            ?? ($existing['type'] ?? null),
                'productVariants' => $b['productVariants'] ?? ($existing['productVariants'] ?? []),
            ],
        );
    }
}

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
    name: 'update_product_image',
    description: <<<'DESC'
update_product_image(productCode, imageId, body) → JSON of the updated product image.

IMPORTANT: First call get_product_image to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body.
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
        return $this->client->put(
            sprintf('products/%s/images/%d', $productCode, $imageId),
            json_decode($body, true) ?? [],
        );
    }
}

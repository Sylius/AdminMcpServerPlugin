<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductVariant;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_variant',
    description: <<<'DESC'
update_product_variant(code, body) → JSON of the updated product variant.

IMPORTANT: First call get_product_variant to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body. This preserves all required fields including translation @ids and channelPricings.

Note: channelPricings values are objects with price (int, smallest currency unit, e.g. 3000=30.00).
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code, string $body): string
    {
        return $this->client->put(sprintf('product-variants/%s', $code), json_decode($body, true) ?? []);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAttribute;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_attribute',
    description: <<<'DESC'
update_product_attribute(code, body) → JSON of the updated product attribute.

IMPORTANT: First call get_product_attribute to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body. This preserves all required fields including translation @ids.
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
        return $this->client->put(sprintf('product-attributes/%s', $code), json_decode($body, true) ?? []);
    }
}

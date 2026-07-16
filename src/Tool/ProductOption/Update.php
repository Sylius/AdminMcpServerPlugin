<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductOption;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_option',
    description: <<<'DESC'
update_product_option(code, body) → JSON of the updated product option.

IMPORTANT: First call get_product_option to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body. This preserves all required fields including translation @ids.
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
        return $this->client->put(sprintf('product-options/%s', $code), json_decode($body, true) ?? []);
    }
}

<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductAttribute;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_product_attribute',
    description: 'get_product_attribute(code) → JSON object of a single Sylius product attribute. Returns: code, type, configuration, storageType, position, translatable, translations.',
)]
final readonly class Show
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code Product attribute code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('product-attributes/%s', $code));
    }
}

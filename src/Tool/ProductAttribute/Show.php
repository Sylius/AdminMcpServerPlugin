<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAttribute;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_product_attribute',
    description: 'get_product_attribute(code) → JSON object of a single Sylius product attribute. Returns: code, type, configuration, storageType, position, translatable, translations.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
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

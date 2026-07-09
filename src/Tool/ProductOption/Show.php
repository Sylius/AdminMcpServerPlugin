<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductOption;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_product_option',
    description: 'get_product_option(code) → JSON object of a single Sylius product option. Returns: code, position, values (IRIs), translations (name per locale).',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Product option code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('product-options/%s', $code));
    }
}

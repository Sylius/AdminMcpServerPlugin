<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Product;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_product',
    description: 'get_product(code) → JSON object of a single Sylius product. Returns: id, code, enabled, channels, mainTaxon, translations (name, slug, description, shortDescription per locale), variants, attributes, options, images, createdAt, updatedAt.',
)]
final readonly class Show
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code Product code (e.g. "MUG_BLUE").
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('products/%s', $code));
    }
}

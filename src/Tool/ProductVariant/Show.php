<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductVariant;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_product_variant',
    description: 'get_product_variant(code) → JSON object of a single Sylius product variant. Returns: code, product, enabled, onHand, onHold, tracked, weight, width, height, depth, taxCategory, shippingCategory, channelPricings, optionValues, translations.',
)]
final readonly class Show
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code Product variant code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('product-variants/%s', $code));
    }
}

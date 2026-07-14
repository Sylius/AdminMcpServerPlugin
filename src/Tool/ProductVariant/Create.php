<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductVariant;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_product_variant',
    description: <<<'DESC'
create_product_variant — Creates a product variant (every product needs at least one variant to be purchasable — the variant holds the price and stock).

REQUIRED: code (unique variant ID, e.g. "SUMMER_HAT_001-default"), productCode (the product this variant belongs to), price (price in smallest currency unit: 1000 = 10.00 EUR/USD).
OPTIONAL: name (variant name), onHand (stock quantity, default 0), enabled (default true), tracked (track stock levels, default false), localeCode (default "en_US").
CHANNEL PRICES: The price is automatically applied to ALL channels the product belongs to (Sylius requires pricing for every channel). To set different prices per channel, pass channelPrices as JSON, e.g. '{"FASHION_WEB":2500,"WEB_EUR":2200}'. If channelPrices is provided it overrides the single price parameter.

IMPORTANT: After creating a product, always create at least one variant with a price. Ask user for the price if not provided. Suggest code = productCode + "-default" for a single variant.
DESC,
)]
final readonly class Create
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(
        string $code,
        string $productCode,
        int $price = 0,
        string $channelPrices = '',
        string $name = '',
        string $localeCode = 'en_US',
        int $onHand = 0,
        bool $enabled = true,
        bool $tracked = false,
    ): string {
        // Auto-fetch product channels so we can price all of them (Sylius requires it)
        $product = json_decode($this->client->get(sprintf('products/%s', $productCode)), true);
        $productChannelCodes = array_map(
            static fn (string $iri) => basename($iri),
            $product['channels'] ?? [],
        );

        // Build channelPricings: one price for all channels, then override with specific values
        $pricings = [];
        foreach ($productChannelCodes as $ch) {
            $pricings[$ch] = ['price' => $price];
        }

        if ($channelPrices !== '') {
            $overrides = json_decode($channelPrices, true) ?? [];
            foreach ($overrides as $ch => $p) {
                $pricings[$ch] = ['price' => (int) $p];
            }
        }

        $body = [
            'code'            => $code,
            'product'         => sprintf('/api/v2/admin/products/%s', $productCode),
            'enabled'         => $enabled,
            'tracked'         => $tracked,
            'onHand'          => $onHand,
            'channelPricings' => $pricings,
        ];

        if ($name !== '') {
            $body['translations'] = [
                $localeCode => ['locale' => $localeCode, 'name' => $name],
            ];
        }

        return $this->client->post('product-variants', $body);
    }
}

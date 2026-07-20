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

namespace Sylius\AdminMcpServerPlugin\Tool\ProductVariant;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_product_variant',
    description: <<<'DESC'
create_product_variant — Creates a product variant (every product needs at least one variant to be purchasable — the variant holds the price and stock).

REQUIRED: code (unique variant ID, e.g. "SUMMER_HAT_001-default"), product (IRI from list_products @id, e.g. "/api/v2/admin/products/SUMMER_HAT"), price (price in smallest currency unit: 1000 = 10.00 EUR/USD).
OPTIONAL: name (variant name), onHand (stock quantity, default 0), enabled (default true), tracked (track stock levels, default false), localeCode (default "en_US").
CHANNEL PRICES: The price is automatically applied to ALL channels the product belongs to (Sylius requires pricing for every channel). To set different prices per channel, pass channelPrices as JSON string, e.g. '{"FASHION_WEB":2500,"WEB_EUR":2200}'. If channelPrices is provided it overrides the single price parameter.
WARNING: If the product has no channels assigned yet (channels: []), channelPricings will be empty and the variant won't have a price — use update_product(code, channels=["FASHION_WEB"]) to assign channels BEFORE creating the variant.

IMPORTANT: After creating a product, always create at least one variant with a price. Ask user for the price if not provided. Suggest code = product code + "-default" for a single variant.
DESC,
)]
final readonly class Create
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(
        string $code,
        string $product,
        int $price = 0,
        string $channelPrices = '',
        string $name = '',
        string $localeCode = 'en_US',
        int $onHand = 0,
        bool $enabled = true,
        bool $tracked = false,
    ): string {
        // Auto-fetch product channels so we can price all of them (Sylius requires it)
        $productData = json_decode($this->client->get(sprintf('products/%s', basename($product))), true);
        $productChannelCodes = array_map(
            static fn (string $iri) => basename($iri),
            $productData['channels'] ?? [],
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
            'code' => $code,
            'product' => $product,
            'enabled' => $enabled,
            'tracked' => $tracked,
            'onHand' => $onHand,
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

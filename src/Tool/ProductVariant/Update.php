<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductVariant;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_variant',
    description: <<<'DESC'
update_product_variant — Updates a product variant. Only provided fields are changed; omitted fields keep their current values.

OPTIONAL: name (variant display name), onHand (stock quantity), enabled, tracked (stock tracking), localeCode (default "en_US").
PRICE UPDATE: To change the price for a single channel pass price + channelCode (e.g. channelCode="FASHION_WEB", price=3000). To update prices for multiple channels at once pass channelPrices as JSON, e.g. '{"FASHION_WEB":3000,"WEB_EUR":2800}'. If channelPrices is given, channelCode/price are ignored.

The variant code identifies which variant to update. Get variant codes from list_product_variants or get_product.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(
        string $code,
        string $name = '',
        string $localeCode = 'en_US',
        ?int $onHand = null,
        ?bool $enabled = null,
        ?bool $tracked = null,
        ?int $price = null,
        string $channelCode = '',
        string $channelPrices = '',
    ): string {
        // Fetch existing variant to merge changes into (required for ld+json PUT)
        $existing = json_decode($this->client->get(sprintf('product-variants/%s', $code)), true);

        if ($enabled !== null) {
            $existing['enabled'] = $enabled;
        }
        if ($tracked !== null) {
            $existing['tracked'] = $tracked;
        }
        if ($onHand !== null) {
            $existing['onHand'] = $onHand;
        }

        if ($channelPrices !== '') {
            $overrides = json_decode($channelPrices, true) ?? [];
            foreach ($overrides as $ch => $p) {
                if (isset($existing['channelPricings'][$ch])) {
                    $existing['channelPricings'][$ch]['price'] = (int) $p;
                }
            }
        } elseif ($price !== null && $channelCode !== '') {
            if (isset($existing['channelPricings'][$channelCode])) {
                $existing['channelPricings'][$channelCode]['price'] = $price;
            }
        }

        if ($name !== '') {
            if (isset($existing['translations'][$localeCode])) {
                $existing['translations'][$localeCode]['name'] = $name;
            } else {
                $existing['translations'][$localeCode] = [
                    '@id' => $this->client->iri(sprintf('product-variants/%s/translations/%s', $code, $localeCode)),
                    'locale' => $localeCode,
                    'name'   => $name,
                ];
            }
        }

        return $this->client->put(sprintf('product-variants/%s', $code), $existing);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductVariant;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_variant',
    description: <<<'DESC'
update_product_variant(code, body) → JSON of the updated product variant. Only fields in body are changed.

body (JSON string) — fields: enabled (bool), tracked (bool), onHand (int), translations (map of locale → {name?}), channelPricings (map of channel code → {price: int} — price in smallest currency unit, e.g. 3000=30.00).
Shorthand for single channel: pass price (int) + channelCode (string) in body instead of channelPricings.
Example: '{"enabled":true,"onHand":50,"channelPricings":{"FASHION_WEB":{"price":2999},"WEB_EUR":{"price":2799}}}'
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
        $existing = json_decode($this->client->get(sprintf('product-variants/%s', $code)), true);
        $b = json_decode($body, true) ?? [];

        if (isset($b['enabled'])) {
            $existing['enabled'] = $b['enabled'];
        }
        if (isset($b['tracked'])) {
            $existing['tracked'] = $b['tracked'];
        }
        if (isset($b['onHand'])) {
            $existing['onHand'] = $b['onHand'];
        }

        if (isset($b['channelPricings'])) {
            foreach ($b['channelPricings'] as $ch => $pricing) {
                if (isset($existing['channelPricings'][$ch])) {
                    $existing['channelPricings'][$ch]['price'] = (int) $pricing['price'];
                }
            }
        } elseif (isset($b['price'], $b['channelCode'])) {
            if (isset($existing['channelPricings'][$b['channelCode']])) {
                $existing['channelPricings'][$b['channelCode']]['price'] = (int) $b['price'];
            }
        }

        if (isset($b['translations'])) {
            foreach ($b['translations'] as $locale => $fields) {
                if (isset($existing['translations'][$locale])) {
                    foreach ($fields as $key => $value) {
                        $existing['translations'][$locale][$key] = $value;
                    }
                } else {
                    $existing['translations'][$locale] = $fields;
                }
            }
        }

        return $this->client->put(sprintf('product-variants/%s', $code), $existing);
    }
}

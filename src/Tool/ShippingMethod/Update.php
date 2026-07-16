<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_shipping_method',
    description: <<<'DESC'
update_shipping_method(code, body) → JSON of the updated shipping method. Only fields in body are changed.

body (JSON string) — fields: translations (map of locale → {name?, description?}), zone (IRI e.g. "/api/v2/admin/zones/WORLD"), calculator ("flat_rate"/"per_unit_rate"/"percentage_discount"), channels (array of channel IRIs from list_channels @id), amount (int, smallest currency unit e.g. 500=5.00), percentage (float, e.g. 0.1=10%), category (IRI from list_shipping_categories @id), taxCategory (IRI from list_tax_categories @id), enabled (bool).
Configuration is automatically built for all channels in the system.
Example: '{"translations":{"en_US":{"name":"Express","description":"Next day delivery"},"pl_PL":{"name":"Ekspres"}},"amount":999}'
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
        $existing = json_decode($this->client->get(sprintf('shipping-methods/%s', $code)), true);
        $b = json_decode($body, true) ?? [];

        $resolvedCalculator = $b['calculator'] ?? ($existing['shippingChargesCalculator'] ?? 'flat_rate');
        $resolvedZone       = $b['zone']       ?? ($existing['zone'] ?? '');
        $resolvedChannels   = $b['channels']   ?? ($existing['channels'] ?? []);
        $amount             = $b['amount']     ?? null;
        $percentage         = $b['percentage'] ?? null;

        $allChannels = json_decode($this->client->get('channels', ['pagination' => false]), true);
        $configuration = [];
        foreach ($allChannels['hydra:member'] ?? [] as $channel) {
            $channelCode = $channel['code'];
            $existingConfig = $existing['shippingChargesCalculatorConfiguration'][$channelCode] ?? [];
            if ($resolvedCalculator === 'percentage_discount') {
                $configuration[$channelCode] = ['percentage' => $percentage ?? ($existingConfig['percentage'] ?? 0.0)];
            } else {
                $configuration[$channelCode] = ['amount' => $amount ?? ($existingConfig['amount'] ?? 0)];
            }
        }

        $mergedTranslations = $existing['translations'] ?? [];
        if (isset($b['translations'])) {
            foreach ($b['translations'] as $locale => $fields) {
                if (!isset($mergedTranslations[$locale])) {
                    $mergedTranslations[$locale] = [];
                }
                foreach ($fields as $key => $value) {
                    $mergedTranslations[$locale][$key] = $value;
                }
            }
        }

        $merged = [
            'calculator'    => $resolvedCalculator,
            'configuration' => $configuration,
            'zone'          => $resolvedZone,
            'channels'      => $resolvedChannels,
            'translations'  => $mergedTranslations,
        ];

        if (isset($b['enabled'])) {
            $merged['enabled'] = $b['enabled'];
        } elseif (isset($existing['enabled'])) {
            $merged['enabled'] = $existing['enabled'];
        }

        foreach (['category', 'taxCategory'] as $opt) {
            if (isset($b[$opt])) {
                $merged[$opt] = $b[$opt];
            } elseif (isset($existing[$opt])) {
                $merged[$opt] = $existing[$opt];
            }
        }

        return $this->client->put(sprintf('shipping-methods/%s', $code), $merged);
    }
}

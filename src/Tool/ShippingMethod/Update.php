<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_shipping_method',
    description: <<<'DESC'
update_shipping_method — Updates a shipping method. Only provided fields are changed; omitted fields keep their current values.

REQUIRED: code (the shipping method code to update).
OPTIONAL: name, zoneCode, calculator (flat_rate/per_unit_rate/percentage_discount), channelCodes, amount (smallest unit, e.g. 500=5.00), percentage (decimal, e.g. 0.1=10%), localeCode, description, categoryCode, taxCategoryCode, enabled.

Configuration is automatically built for all channels in the system.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string[]  $channelCodes    Channel codes this method is available in.
     */
    public function __invoke(
        string $code,
        string $name = '',
        string $zoneCode = '',
        string $calculator = '',
        array $channelCodes = [],
        int $amount = -1,
        float $percentage = -1.0,
        string $localeCode = 'en_US',
        string $description = '',
        string $categoryCode = '',
        string $taxCategoryCode = '',
        ?bool $enabled = null,
    ): string {
        $existing = json_decode($this->client->get(sprintf('shipping-methods/%s', $code)), true);

        $resolvedCalculator = $calculator !== '' ? $calculator : ($existing['shippingChargesCalculator'] ?? 'flat_rate');
        $resolvedZone = $zoneCode !== ''
            ? sprintf('/api/v2/admin/zones/%s', $zoneCode)
            : ($existing['zone'] ?? '');

        $resolvedChannels = $channelCodes !== []
            ? array_map(static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c), $channelCodes)
            : ($existing['channels'] ?? []);

        $allChannels = json_decode($this->client->get('channels', ['pagination' => false]), true);
        $configuration = [];
        foreach ($allChannels['hydra:member'] ?? [] as $channel) {
            $channelCode = $channel['code'];
            $existingConfig = $existing['shippingChargesCalculatorConfiguration'][$channelCode] ?? [];
            if ($resolvedCalculator === 'percentage_discount') {
                $resolvedPercentage = $percentage >= 0.0 ? $percentage : ($existingConfig['percentage'] ?? 0.0);
                $configuration[$channelCode] = ['percentage' => $resolvedPercentage];
            } else {
                $resolvedAmount = $amount >= 0 ? $amount : ($existingConfig['amount'] ?? 0);
                $configuration[$channelCode] = ['amount' => $resolvedAmount];
            }
        }

        $translations = $existing['translations'] ?? [];
        if ($name !== '') {
            if (!isset($translations[$localeCode])) {
                $translations[$localeCode] = ['locale' => $localeCode];
            }
            $translations[$localeCode]['name'] = $name;
        }
        if ($description !== '') {
            if (!isset($translations[$localeCode])) {
                $translations[$localeCode] = ['locale' => $localeCode];
            }
            $translations[$localeCode]['description'] = $description;
        }

        $body = [
            'calculator'    => $resolvedCalculator,
            'configuration' => $configuration,
            'zone'          => $resolvedZone,
            'channels'      => $resolvedChannels,
            'translations'  => $translations,
        ];

        if ($enabled !== null) {
            $body['enabled'] = $enabled;
        }

        $existingCategory = $existing['category'] ?? null;
        if ($categoryCode !== '') {
            $body['category'] = sprintf('/api/v2/admin/shipping-categories/%s', $categoryCode);
        } elseif ($existingCategory !== null) {
            $body['category'] = $existingCategory;
        }

        $existingTaxCategory = $existing['taxCategory'] ?? null;
        if ($taxCategoryCode !== '') {
            $body['taxCategory'] = sprintf('/api/v2/admin/tax-categories/%s', $taxCategoryCode);
        } elseif ($existingTaxCategory !== null) {
            $body['taxCategory'] = $existingTaxCategory;
        }

        return $this->client->put(sprintf('shipping-methods/%s', $code), $body);
    }
}

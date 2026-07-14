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
OPTIONAL: name, zone (IRI e.g. "/api/v2/admin/zones/WORLD"), calculator (flat_rate/per_unit_rate/percentage_discount), channels (array of channel IRIs), amount (smallest unit, e.g. 500=5.00), percentage (decimal, e.g. 0.1=10%), localeCode, description, category (IRI), taxCategory (IRI), enabled.

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
     * @param string[]  $channels    Channel IRIs this method is available in (e.g. ["/api/v2/admin/channels/WEB"]).
     */
    public function __invoke(
        string $code,
        string $name = '',
        string $zone = '',
        string $calculator = '',
        array $channels = [],
        int $amount = -1,
        float $percentage = -1.0,
        string $localeCode = 'en_US',
        string $description = '',
        string $category = '',
        string $taxCategory = '',
        ?bool $enabled = null,
    ): string {
        $existing = json_decode($this->client->get(sprintf('shipping-methods/%s', $code)), true);

        $resolvedCalculator = $calculator !== '' ? $calculator : ($existing['shippingChargesCalculator'] ?? 'flat_rate');
        $resolvedZone = $zone !== '' ? $zone : ($existing['zone'] ?? '');
        $resolvedChannels = $channels !== [] ? $channels : ($existing['channels'] ?? []);

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
        if ($category !== '') {
            $body['category'] = $category;
        } elseif ($existingCategory !== null) {
            $body['category'] = $existingCategory;
        }

        $existingTaxCategory = $existing['taxCategory'] ?? null;
        if ($taxCategory !== '') {
            $body['taxCategory'] = $taxCategory;
        } elseif ($existingTaxCategory !== null) {
            $body['taxCategory'] = $existingTaxCategory;
        }

        return $this->client->put(sprintf('shipping-methods/%s', $code), $body);
    }
}

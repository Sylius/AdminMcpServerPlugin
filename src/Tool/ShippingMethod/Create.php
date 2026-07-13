<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_shipping_method',
    description: 'create_shipping_method(code, name, zoneCode, calculator, channelCodes, amount?, percentage?, localeCode?, description?, categoryCode?, taxCategoryCode?, enabled?) → JSON of the newly created Sylius shipping method. calculator: "flat_rate" | "per_unit_rate" | "percentage_discount". For flat_rate/per_unit_rate provide amount in smallest currency unit (e.g. 1000 = 10.00). For percentage_discount provide percentage as decimal (0.1 = 10%). Configuration is automatically built for all channels in the system.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $code            Unique shipping method code.
     * @param string   $name            Display name for the given locale.
     * @param string   $zoneCode        Zone code (e.g. "WORLD", "EU"). Use list_zones to find available codes.
     * @param string   $calculator      Calculator name: "flat_rate", "per_unit_rate", "percentage_discount".
     * @param string[] $channelCodes    Channel codes this method will be available in. Use list_channels to find codes.
     * @param int      $amount          Amount in smallest currency unit for flat_rate/per_unit_rate (e.g. 1000 = 10.00).
     * @param float    $percentage      Decimal percentage for percentage_discount (e.g. 0.1 = 10%).
     * @param string   $localeCode      Locale for the name/description translation. Default = "en_US".
     * @param string   $description     Optional description. Default = "".
     * @param string   $categoryCode    Shipping category code. Empty = no category.
     * @param string   $taxCategoryCode Tax category code. Empty = no tax category.
     * @param bool     $enabled         Whether the method is enabled. Default = true.
     */
    public function __invoke(
        string $code,
        string $name,
        string $zoneCode,
        string $calculator,
        array $channelCodes,
        int $amount = 0,
        float $percentage = 0.0,
        string $localeCode = 'en_US',
        string $description = '',
        string $categoryCode = '',
        string $taxCategoryCode = '',
        bool $enabled = true,
    ): string {
        $allChannels = json_decode($this->client->get('channels', ['pagination' => false]), true);
        $configuration = [];
        foreach ($allChannels['hydra:member'] ?? [] as $channel) {
            $channelCode = $channel['code'];
            if ($calculator === 'percentage_discount') {
                $configuration[$channelCode] = ['percentage' => $percentage];
            } else {
                $configuration[$channelCode] = ['amount' => $amount];
            }
        }

        $translation = ['locale' => $localeCode, 'name' => $name];
        if ($description !== '') {
            $translation['description'] = $description;
        }

        $body = [
            'code' => $code,
            'enabled' => $enabled,
            'calculator' => $calculator,
            'configuration' => $configuration,
            'zone' => sprintf('/api/v2/admin/zones/%s', $zoneCode),
            'channels' => array_map(
                static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c),
                $channelCodes,
            ),
            'translations' => [$localeCode => $translation],
        ];

        if ($categoryCode !== '') {
            $body['category'] = sprintf('/api/v2/admin/shipping-categories/%s', $categoryCode);
        }
        if ($taxCategoryCode !== '') {
            $body['taxCategory'] = sprintf('/api/v2/admin/tax-categories/%s', $taxCategoryCode);
        }

        return $this->client->post('shipping-methods', $body);
    }
}

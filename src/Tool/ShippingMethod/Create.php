<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_shipping_method',
    description: 'create_shipping_method(code, name, zoneCode, calculator, channelCodes, configuration?, localeCode?, description?, categoryCode?, taxCategoryCode?, enabled?) → JSON of the newly created Sylius shipping method. calculator: "flat_rate" | "per_unit_rate" | "percentage_discount". configuration example for flat_rate: {"CHANNEL_CODE": {"amount": 500}} (amount in smallest currency unit). For percentage_discount: {"CHANNEL_CODE": {"percentage": 0.1}}.',
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
     * @param string   $zoneCode        Zone code (e.g. "WORLD", "EU"). Converted to IRI internally.
     * @param string   $calculator      Calculator name: "flat_rate", "per_unit_rate", "percentage_discount".
     * @param string[] $channelCodes    Channel codes this method is available in.
     * @param array    $configuration   Calculator configuration keyed by channel code. E.g. {"FASHION_WEB": {"amount": 500}}.
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
        array $configuration = [],
        string $localeCode = 'en_US',
        string $description = '',
        string $categoryCode = '',
        string $taxCategoryCode = '',
        bool $enabled = true,
    ): string {
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

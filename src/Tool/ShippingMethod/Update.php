<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_shipping_method',
    description: 'update_shipping_method(code, name, zoneCode, calculator, channelCodes, configuration?, localeCode?, description?, categoryCode?, taxCategoryCode?, enabled?) → JSON of the updated Sylius shipping method. Uses PUT — all required fields must be provided. Fetches existing translations to avoid overwriting other locales.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $code            Shipping method code to update.
     * @param string   $name            Display name for the given locale.
     * @param string   $zoneCode        Zone code (e.g. "WORLD").
     * @param string   $calculator      Calculator name: "flat_rate", "per_unit_rate", "percentage_discount".
     * @param string[] $channelCodes    Channel codes this method is available in.
     * @param array    $configuration   Calculator configuration keyed by channel code.
     * @param string   $localeCode      Locale for the translation. Default = "en_US".
     * @param string   $description     Description for the given locale. Default = "".
     * @param string   $categoryCode    Shipping category code. Empty = remove category.
     * @param string   $taxCategoryCode Tax category code. Empty = remove tax category.
     * @param bool|null $enabled        Set enabled status. Null = preserve current value.
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
        ?bool $enabled = null,
    ): string {
        $existing = json_decode($this->client->get(sprintf('shipping-methods/%s', $code)), true);
        $translations = $existing['translations'] ?? [];

        if (!isset($translations[$localeCode])) {
            $translations[$localeCode] = ['locale' => $localeCode];
        }
        $translations[$localeCode]['name'] = $name;
        if ($description !== '') {
            $translations[$localeCode]['description'] = $description;
        }

        $body = [
            'calculator' => $calculator,
            'configuration' => $configuration,
            'zone' => sprintf('/api/v2/admin/zones/%s', $zoneCode),
            'channels' => array_map(
                static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c),
                $channelCodes,
            ),
            'translations' => $translations,
        ];

        if ($enabled !== null) {
            $body['enabled'] = $enabled;
        }

        $body['category'] = $categoryCode !== ''
            ? sprintf('/api/v2/admin/shipping-categories/%s', $categoryCode)
            : null;

        $body['taxCategory'] = $taxCategoryCode !== ''
            ? sprintf('/api/v2/admin/tax-categories/%s', $taxCategoryCode)
            : null;

        return $this->client->put(sprintf('shipping-methods/%s', $code), $body);
    }
}

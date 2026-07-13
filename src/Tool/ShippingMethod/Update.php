<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_shipping_method',
    description: 'update_shipping_method(code, name, zoneCode, calculator, channelCodes, amount?, percentage?, localeCode?, description?, categoryCode?, taxCategoryCode?, enabled?) → JSON of the updated Sylius shipping method. Uses PUT — all required fields must be provided. For flat_rate/per_unit_rate provide amount (smallest currency unit). For percentage_discount provide percentage as decimal (0.1 = 10%). Configuration is automatically built for all channels in the system.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string    $code            Shipping method code to update.
     * @param string    $name            Display name for the given locale.
     * @param string    $zoneCode        Zone code (e.g. "WORLD").
     * @param string    $calculator      Calculator name: "flat_rate", "per_unit_rate", "percentage_discount".
     * @param string[]  $channelCodes    Channel codes this method is available in.
     * @param int       $amount          Amount in smallest currency unit for flat_rate/per_unit_rate.
     * @param float     $percentage      Decimal percentage for percentage_discount (0.1 = 10%).
     * @param string    $localeCode      Locale for the translation. Default = "en_US".
     * @param string    $description     Description for the given locale. Default = "".
     * @param string    $categoryCode    Shipping category code. Empty = remove category.
     * @param string    $taxCategoryCode Tax category code. Empty = remove tax category.
     * @param bool|null $enabled         Set enabled status. Null = preserve current value.
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
        ?bool $enabled = null,
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

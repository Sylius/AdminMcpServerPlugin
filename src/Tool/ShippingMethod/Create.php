<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_shipping_method',
    description: <<<'DESC'
create_shipping_method — Creates a shipping method (delivery option shown to customers at checkout). Prerequisites: run list_zones to get zoneCode; run list_channels to get channelCodes.

REQUIRED: code (unique ID, e.g. "DHL_EXPRESS"), name (e.g. "DHL Express"), zoneCode (delivery zone, e.g. "WORLD" or "EU"), calculator (pricing type), channelCodes (which shops offer this shipping).

calculator types:
- "flat_rate" — same price regardless of order size; provide amount (in smallest unit: 1000 = 10.00)
- "per_unit_rate" — price × number of items; provide amount per item
- "percentage_discount" — reduces shipping cost by a percentage; provide percentage (0.1 = 10% off, 1.0 = free)

amount is in the smallest currency unit (e.g. 500 = 5.00 EUR/USD).
The system automatically sets the rate for all channels — just provide the amount or percentage once.

Ask user: what should the shipping cost be? Which zones/regions does it apply to?
DESC,
)]
final readonly class Create
{
    public function __construct(private ApiClientInterface $client) {}

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
        if ($description !== '') { $translation['description'] = $description; }

        $body = [
            'code'          => $code,
            'enabled'       => $enabled,
            'calculator'    => $calculator,
            'configuration' => $configuration,
            'zone'          => sprintf('/api/v2/admin/zones/%s', $zoneCode),
            'channels'      => array_map(
                static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c),
                $channelCodes,
            ),
            'translations'  => [$localeCode => $translation],
        ];

        if ($categoryCode !== '') { $body['category'] = sprintf('/api/v2/admin/shipping-categories/%s', $categoryCode); }
        if ($taxCategoryCode !== '') { $body['taxCategory'] = sprintf('/api/v2/admin/tax-categories/%s', $taxCategoryCode); }

        return $this->client->post('shipping-methods', $body);
    }
}

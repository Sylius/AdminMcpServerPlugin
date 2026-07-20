<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_shipping_method',
    description: <<<'DESC'
create_shipping_method — Creates a shipping method (delivery option shown to customers at checkout). Prerequisites: run list_zones to get zone IRI; run list_channels to get channel IRIs.

REQUIRED: code (unique ID, e.g. "DHL_EXPRESS"), name (e.g. "DHL Express"), zone (zone IRI, e.g. "/api/v2/admin/zones/WORLD"), calculator (pricing type), channels (array of channel IRIs).

calculator types — also specify the matching parameter:
- "flat_rate" — same price regardless of order size; also pass amount=1000 (1000 = 10.00 EUR/USD, smallest currency unit)
- "per_unit_rate" — price × number of items; also pass amount=500 (price per item in smallest currency unit)
- "percentage_discount" — reduces shipping cost by a percentage; also pass percentage=0.1 (0.1 = 10% off, 1.0 = free)

The system automatically applies the amount/percentage to all channels — just provide once.
NOTE: the price parameter is called "amount" (not "price"). Pass amount=<value>.

Ask user: what should the shipping cost be? Which zones/regions does it apply to?
DESC,
)]
final readonly class Create
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    /**
     * @param string[]  $channels    Channel IRIs this method is available in (e.g. ["/api/v2/admin/channels/WEB"]).
     */
    public function __invoke(
        string $code,
        string $name,
        string $zone,
        string $calculator,
        array $channels,
        int $amount = 0,
        float $percentage = 0.0,
        string $localeCode = 'en_US',
        string $description = '',
        string $category = '',
        string $taxCategory = '',
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
            'zone' => $zone,
            'channels' => $channels,
            'translations' => [$localeCode => $translation],
        ];

        if ($category !== '') {
            $body['category'] = $category;
        }
        if ($taxCategory !== '') {
            $body['taxCategory'] = $taxCategory;
        }

        return $this->client->post('shipping-methods', $body);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Channel;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_channel',
    description: 'update_channel(code, name, localeCode, currencyCode, taxCalculationStrategy?, hostname?, color?, enabled?, taxZoneCode?, contactEmail?, shippingAddressInCheckoutRequired?) → JSON object of the updated Sylius channel. Uses PUT. taxCalculationStrategy: "order_items_based" or "order_item_units_based".',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string      $code                             Channel code to update.
     * @param string      $name                             Channel display name.
     * @param string      $localeCode                       Default locale code (e.g. "en_US").
     * @param string      $currencyCode                     Base currency code (e.g. "USD").
     * @param string      $taxCalculationStrategy           Tax calculation strategy. Default = "order_item_units_based".
     * @param string      $hostname                         Channel hostname. Default = "".
     * @param string      $color                            UI color hex. Default = "".
     * @param bool        $enabled                          Whether the channel is enabled. Default = true.
     * @param string      $taxZoneCode                      Tax zone code. Default = "".
     * @param string      $contactEmail                     Contact email. Default = "".
     * @param bool        $shippingAddressInCheckoutRequired Whether shipping address is required. Default = false.
     */
    public function __invoke(
        string $code,
        string $name,
        string $localeCode,
        string $currencyCode,
        string $taxCalculationStrategy = 'order_item_units_based',
        string $hostname = '',
        string $color = '',
        bool $enabled = true,
        string $taxZoneCode = '',
        string $contactEmail = '',
        bool $shippingAddressInCheckoutRequired = false,
    ): string {
        $body = [
            'name' => $name,
            'enabled' => $enabled,
            'taxCalculationStrategy' => $taxCalculationStrategy,
            'shippingAddressInCheckoutRequired' => $shippingAddressInCheckoutRequired,
            'defaultLocale' => sprintf('/api/v2/admin/locales/%s', $localeCode),
            'baseCurrency' => sprintf('/api/v2/admin/currencies/%s', $currencyCode),
            'locales' => [sprintf('/api/v2/admin/locales/%s', $localeCode)],
            'currencies' => [sprintf('/api/v2/admin/currencies/%s', $currencyCode)],
        ];

        if ($hostname !== '') {
            $body['hostname'] = $hostname;
        }
        if ($color !== '') {
            $body['color'] = $color;
        }
        if ($taxZoneCode !== '') {
            $body['taxZone'] = sprintf('/api/v2/admin/zones/%s', $taxZoneCode);
        }
        if ($contactEmail !== '') {
            $body['contactEmail'] = $contactEmail;
        }

        return $this->client->put(sprintf('channels/%s', $code), $body);
    }
}

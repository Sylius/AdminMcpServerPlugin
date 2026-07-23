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

namespace Sylius\AdminMcpServerPlugin\Tool\Channel;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_channel',
    description: 'create_channel(code, name, locale, currency, taxCalculationStrategy?, hostname?, color?, enabled?, taxZone?, contactEmail?, shippingAddressInCheckoutRequired?) → JSON object of the newly created Sylius channel. taxCalculationStrategy: "order_items_based" or "order_item_units_based" (default). locale e.g. "/api/v2/admin/locales/en_US", currency e.g. "/api/v2/admin/currencies/USD", taxZone e.g. "/api/v2/admin/zones/EU".',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string      $code                             Unique channel code (e.g. "WEB").
     * @param string      $name                             Channel display name.
     * @param string      $locale                           Default locale IRI (e.g. "/api/v2/admin/locales/en_US").
     * @param string      $currency                         Base currency IRI (e.g. "/api/v2/admin/currencies/USD").
     * @param string      $taxCalculationStrategy           Tax calculation strategy. Default = "order_item_units_based".
     * @param string      $hostname                         Channel hostname. Default = "".
     * @param string      $color                            UI color hex. Default = "".
     * @param bool        $enabled                          Whether the channel is enabled. Default = true.
     * @param string      $taxZone                          Tax zone IRI (e.g. "/api/v2/admin/zones/EU"). Default = "".
     * @param string      $contactEmail                     Contact email. Default = "".
     * @param bool        $shippingAddressInCheckoutRequired Whether shipping address is required. Default = false.
     */
    public function __invoke(
        string $code,
        string $name,
        string $locale,
        string $currency,
        string $taxCalculationStrategy = 'order_item_units_based',
        string $hostname = '',
        string $color = '',
        bool $enabled = true,
        string $taxZone = '',
        string $contactEmail = '',
        bool $shippingAddressInCheckoutRequired = false,
    ): string {
        $body = [
            'code' => $code,
            'name' => $name,
            'enabled' => $enabled,
            'taxCalculationStrategy' => $taxCalculationStrategy,
            'shippingAddressInCheckoutRequired' => $shippingAddressInCheckoutRequired,
            'defaultLocale' => $locale,
            'baseCurrency' => $currency,
            'locales' => [$locale],
            'currencies' => [$currency],
        ];

        if ($hostname !== '') {
            $body['hostname'] = $hostname;
        }
        if ($color !== '') {
            $body['color'] = $color;
        }
        if ($taxZone !== '') {
            $body['defaultTaxZone'] = $taxZone;
        }
        if ($contactEmail !== '') {
            $body['contactEmail'] = $contactEmail;
        }

        return $this->client->post('channels', $body);
    }
}

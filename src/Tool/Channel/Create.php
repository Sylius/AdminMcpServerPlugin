<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Channel;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_channel',
    description: 'create_channel(code, name, localeCode, currencyCode, taxCalculationStrategy?, hostname?, color?, enabled?, taxZoneCode?, contactEmail?, shippingAddressInCheckoutRequired?) → JSON object of the newly created Sylius channel. taxCalculationStrategy: "order_items_based" or "order_item_units_based" (default). localeCode e.g. "en_US", currencyCode e.g. "USD".',
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
            'code' => $code,
            'name' => $name,
            'enabled' => $enabled,
            'taxCalculationStrategy' => $taxCalculationStrategy,
            'shippingAddressInCheckoutRequired' => $shippingAddressInCheckoutRequired,
            'defaultLocale' => $this->client->iri(sprintf('locales/%s', $this->client->normalizeCode($localeCode))),
            'baseCurrency' => $this->client->iri(sprintf('currencies/%s', $this->client->normalizeCode($currencyCode))),
            'locales' => [$this->client->iri(sprintf('locales/%s', $this->client->normalizeCode($localeCode)))],
            'currencies' => [$this->client->iri(sprintf('currencies/%s', $this->client->normalizeCode($currencyCode)))],
        ];

        if ($hostname !== '') {
            $body['hostname'] = $hostname;
        }
        if ($color !== '') {
            $body['color'] = $color;
        }
        if ($taxZoneCode !== '') {
            $body['taxZone'] = $this->client->iri(sprintf('zones/%s', $this->client->normalizeCode($taxZoneCode)));
        }
        if ($contactEmail !== '') {
            $body['contactEmail'] = $contactEmail;
        }

        return $this->client->post('channels', $body);
    }
}

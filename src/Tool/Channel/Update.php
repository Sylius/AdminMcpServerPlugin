<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Channel;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_channel',
    description: 'update_channel(code, name?, localeCode?, currencyCode?, taxCalculationStrategy?, hostname?, color?, enabled?, taxZoneCode?, contactEmail?, shippingAddressInCheckoutRequired?) → JSON object of the updated Sylius channel. Only provided fields are changed; omitted fields keep their current values. taxCalculationStrategy: "order_items_based" or "order_item_units_based".',
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
     * @param string      $taxCalculationStrategy           Tax calculation strategy.
     * @param string      $hostname                         Channel hostname.
     * @param string      $color                            UI color hex (e.g. "#542d9c").
     * @param bool|null   $enabled                          Whether the channel is enabled.
     * @param string      $taxZoneCode                      Tax zone code.
     * @param string      $contactEmail                     Contact email.
     * @param bool|null   $shippingAddressInCheckoutRequired Whether shipping address is required.
     */
    public function __invoke(
        string $code,
        string $name = '',
        string $localeCode = '',
        string $currencyCode = '',
        string $taxCalculationStrategy = '',
        string $hostname = '',
        string $color = '',
        ?bool $enabled = null,
        string $taxZoneCode = '',
        string $contactEmail = '',
        ?bool $shippingAddressInCheckoutRequired = null,
    ): string {
        $existing = json_decode($this->client->get(sprintf('channels/%s', $code)), true);

        $resolvedLocale   = $localeCode !== '' ? $localeCode : basename($existing['defaultLocale'] ?? 'en_US');
        $resolvedCurrency = $currencyCode !== '' ? $currencyCode : basename($existing['baseCurrency'] ?? 'USD');
        $resolvedStrategy = $taxCalculationStrategy !== '' ? $taxCalculationStrategy : ($existing['taxCalculationStrategy'] ?? 'order_item_units_based');

        $body = [
            'name'     => $name !== '' ? $name : ($existing['name'] ?? $code),
            'enabled'  => $enabled ?? ($existing['enabled'] ?? true),
            'taxCalculationStrategy' => $resolvedStrategy,
            'shippingAddressInCheckoutRequired' => $shippingAddressInCheckoutRequired ?? ($existing['shippingAddressInCheckoutRequired'] ?? false),
            'defaultLocale' => $this->client->iri(sprintf('locales/%s', $resolvedLocale)),
            'baseCurrency'  => $this->client->iri(sprintf('currencies/%s', $resolvedCurrency)),
            'locales'    => $localeCode !== ''
                ? [$this->client->iri(sprintf('locales/%s', $localeCode))]
                : ($existing['locales'] ?? [$this->client->iri(sprintf('locales/%s', $resolvedLocale))]),
            'currencies' => $currencyCode !== ''
                ? [$this->client->iri(sprintf('currencies/%s', $currencyCode))]
                : ($existing['currencies'] ?? [$this->client->iri(sprintf('currencies/%s', $resolvedCurrency))]),
        ];

        // Preserve optional fields from existing when not overridden
        $existingHostname = $existing['hostname'] ?? null;
        if ($hostname !== '') {
            $body['hostname'] = $hostname;
        } elseif ($existingHostname !== null) {
            $body['hostname'] = $existingHostname;
        }

        $existingColor = $existing['color'] ?? null;
        if ($color !== '') {
            $body['color'] = $color;
        } elseif ($existingColor !== null) {
            $body['color'] = $existingColor;
        }

        $existingEmail = $existing['contactEmail'] ?? null;
        if ($contactEmail !== '') {
            $body['contactEmail'] = $contactEmail;
        } elseif ($existingEmail !== null) {
            $body['contactEmail'] = $existingEmail;
        }

        $existingZone = $existing['defaultTaxZone'] ?? null;
        if ($taxZoneCode !== '') {
            $body['taxZone'] = $this->client->iri(sprintf('zones/%s', $taxZoneCode));
        } elseif ($existingZone !== null) {
            $body['taxZone'] = $existingZone;
        }

        return $this->client->put(sprintf('channels/%s', $code), $body);
    }
}

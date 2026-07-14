<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Channel;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_channel',
    description: 'update_channel(code, name?, locale?, currency?, taxCalculationStrategy?, hostname?, color?, enabled?, taxZone?, contactEmail?, shippingAddressInCheckoutRequired?) → JSON object of the updated Sylius channel. Only provided fields are changed; omitted fields keep their current values. taxCalculationStrategy: "order_items_based" or "order_item_units_based". locale e.g. "/api/v2/admin/locales/en_US", currency e.g. "/api/v2/admin/currencies/USD", taxZone e.g. "/api/v2/admin/zones/EU".',
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
     * @param string      $locale                           Default locale IRI (e.g. "/api/v2/admin/locales/en_US").
     * @param string      $currency                         Base currency IRI (e.g. "/api/v2/admin/currencies/USD").
     * @param string      $taxCalculationStrategy           Tax calculation strategy.
     * @param string      $hostname                         Channel hostname.
     * @param string      $color                            UI color hex (e.g. "#542d9c").
     * @param bool|null   $enabled                          Whether the channel is enabled.
     * @param string      $taxZone                          Tax zone IRI (e.g. "/api/v2/admin/zones/EU").
     * @param string      $contactEmail                     Contact email.
     * @param bool|null   $shippingAddressInCheckoutRequired Whether shipping address is required.
     */
    public function __invoke(
        string $code,
        string $name = '',
        string $locale = '',
        string $currency = '',
        string $taxCalculationStrategy = '',
        string $hostname = '',
        string $color = '',
        ?bool $enabled = null,
        string $taxZone = '',
        string $contactEmail = '',
        ?bool $shippingAddressInCheckoutRequired = null,
    ): string {
        $existing = json_decode($this->client->get(sprintf('channels/%s', $code)), true);

        $resolvedStrategy = $taxCalculationStrategy !== '' ? $taxCalculationStrategy : ($existing['taxCalculationStrategy'] ?? 'order_item_units_based');

        $body = [
            'name'     => $name !== '' ? $name : ($existing['name'] ?? $code),
            'enabled'  => $enabled ?? ($existing['enabled'] ?? true),
            'taxCalculationStrategy' => $resolvedStrategy,
            'shippingAddressInCheckoutRequired' => $shippingAddressInCheckoutRequired ?? ($existing['shippingAddressInCheckoutRequired'] ?? false),
            'defaultLocale' => $locale !== '' ? $locale : ($existing['defaultLocale'] ?? ''),
            'baseCurrency'  => $currency !== '' ? $currency : ($existing['baseCurrency'] ?? ''),
            'locales'       => $locale !== '' ? [$locale] : ($existing['locales'] ?? []),
            'currencies'    => $currency !== '' ? [$currency] : ($existing['currencies'] ?? []),
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
        if ($taxZone !== '') {
            $body['taxZone'] = $taxZone;
        } elseif ($existingZone !== null) {
            $body['taxZone'] = $existingZone;
        }

        return $this->client->put(sprintf('channels/%s', $code), $body);
    }
}

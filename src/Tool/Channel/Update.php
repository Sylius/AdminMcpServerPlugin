<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Channel;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_channel',
    description: <<<'DESC'
update_channel(code, body) → JSON of the updated channel. Only fields in body are changed.

body (JSON string) — fields: name (string), enabled (bool), taxCalculationStrategy ("order_items_based"/"order_item_units_based"), locale (IRI — sets defaultLocale+locales, e.g. "/api/v2/admin/locales/en_US"), currency (IRI — sets baseCurrency+currencies, e.g. "/api/v2/admin/currencies/USD"), hostname (string), color (hex, e.g. "#542d9c"), contactEmail (string), taxZone (IRI e.g. "/api/v2/admin/zones/EU"), shippingAddressInCheckoutRequired (bool).
Example: '{"name":"Fashion Web","enabled":true}'
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code, string $body): string
    {
        $existing = json_decode($this->client->get(sprintf('channels/%s', $code)), true);
        $b = json_decode($body, true) ?? [];

        $locale   = $b['locale']   ?? null;
        $currency = $b['currency'] ?? null;

        $merged = [
            'name'     => $b['name']     ?? ($existing['name'] ?? $code),
            'enabled'  => $b['enabled']  ?? ($existing['enabled'] ?? true),
            'taxCalculationStrategy' => $b['taxCalculationStrategy'] ?? ($existing['taxCalculationStrategy'] ?? 'order_item_units_based'),
            'shippingAddressInCheckoutRequired' => $b['shippingAddressInCheckoutRequired'] ?? ($existing['shippingAddressInCheckoutRequired'] ?? false),
            'defaultLocale' => $locale ?? ($existing['defaultLocale'] ?? ''),
            'baseCurrency'  => $currency ?? ($existing['baseCurrency'] ?? ''),
            'locales'       => $locale !== null ? [$locale] : ($existing['locales'] ?? []),
            'currencies'    => $currency !== null ? [$currency] : ($existing['currencies'] ?? []),
        ];

        foreach (['hostname', 'color', 'contactEmail'] as $opt) {
            $val = $b[$opt] ?? ($existing[$opt] ?? null);
            if ($val !== null) {
                $merged[$opt] = $val;
            }
        }

        if (isset($b['taxZone'])) {
            $merged['taxZone'] = $b['taxZone'];
        } elseif (isset($existing['defaultTaxZone'])) {
            $merged['taxZone'] = $existing['defaultTaxZone'];
        }

        return $this->client->put(sprintf('channels/%s', $code), $merged);
    }
}

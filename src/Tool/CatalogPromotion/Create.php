<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_catalog_promotion',
    description: <<<'DESC'
create_catalog_promotion — Creates a catalog promotion (automatic discount applied directly to product prices in the catalog, not at checkout). Prerequisites: run list_channels to get channel IRIs; run list_taxons to get taxon codes for scope.

REQUIRED: code (unique ID, e.g. "SUMMER_SALE"), name (internal name), channels (array of channel IRIs from list_channels @id, e.g. ["/api/v2/admin/channels/FASHION_WEB"]).

translations (JSON string) — customer-facing labels per locale. Provide all languages at once (the LLM can generate good translations):
'{"en_US": {"label": "Summer Sale", "description": "10% off all summer items"}, "pl_PL": {"label": "Letnia wyprzedaż", "description": "10% taniej na wszystkie letnie produkty"}}'
If omitted, name is used as label for en_US.

scopes (JSON string) — which products to discount:
- All products: '[]'
- Products in a category/taxon: '[{"type":"for_taxons","configuration":{"taxons":["TAXON_CODE"]}}]'
- Specific product variants: '[{"type":"for_variants","configuration":{"variants":["VARIANT_CODE"]}}]'
- Specific products: '[{"type":"for_products","configuration":{"products":["PRODUCT_CODE"]}}]'

actions (JSON string) — what discount to apply:
- Percentage off (e.g. 20%): '[{"type":"percentage_discount","configuration":{"amount":0.2}}]'
- Fixed amount off per channel: '[{"type":"fixed_discount","configuration":{"CHANNEL_CODE":{"amount":1000}}}]' (amount in smallest unit: 1000 = 10.00)

Ask user: which products/categories to discount? What type of discount (% or fixed amount)?
DESC,
)]
final readonly class Create
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(
        string $code,
        string $name,
        array $channels,
        string $scopes = '[]',
        string $actions = '[]',
        string $translations = '{}',
        bool $enabled = true,
        bool $exclusive = false,
        int $priority = 0,
        string $startDate = '',
        string $endDate = '',
    ): string {
        $decodedTranslations = json_decode($translations, true);
        if (empty($decodedTranslations)) {
            $decodedTranslations = ['en_US' => ['label' => $name]];
        }

        $body = [
            'code'         => $code,
            'name'         => $name,
            'enabled'      => $enabled,
            'exclusive'    => $exclusive,
            'priority'     => $priority,
            'channels'     => $channels,
            'scopes'       => json_decode($scopes, true) ?? [],
            'actions'      => json_decode($actions, true) ?? [],
            'translations' => $decodedTranslations,
        ];

        if ($startDate !== '') { $body['startDate'] = $startDate; }
        if ($endDate !== '') { $body['endDate'] = $endDate; }

        return $this->client->post('catalog-promotions', $body);
    }
}

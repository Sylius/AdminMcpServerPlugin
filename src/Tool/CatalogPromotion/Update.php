<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_catalog_promotion',
    description: <<<'DESC'
update_catalog_promotion(code, body) → JSON of the updated catalog promotion. Only fields in body are changed.

body (JSON string) — fields: name (string), channels (array of channel IRIs from list_channels @id), enabled (bool), exclusive (bool), priority (int), startDate ("YYYY-MM-DDTHH:MM:SS"), endDate ("YYYY-MM-DDTHH:MM:SS"), translations (map of locale → {label?, description?}), scopes (array), actions (array).
- scopes: [{"type":"for_taxons","configuration":{"taxons":["TAXON_CODE"]}}] or [{"type":"for_variants","configuration":{"variants":["VARIANT_CODE"]}}] or [{"type":"for_products","configuration":{"products":["PRODUCT_CODE"]}}]
- actions: [{"type":"percentage_discount","configuration":{"amount":0.2}}] (20% off) or [{"type":"fixed_discount","configuration":{"CHANNEL_CODE":{"amount":1000}}}] (ALL channels required)
Example: '{"name":"Summer Sale","translations":{"en_US":{"label":"Summer Sale"},"pl_PL":{"label":"Letnia Wyprzedaż"}}}'
DESC,
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(string $code, string $body): string
    {
        $existing = json_decode($this->client->get(sprintf('catalog-promotions/%s', $code)), true);
        $b = json_decode($body, true) ?? [];

        $mergedTranslations = $existing['translations'] ?? [];
        if (isset($b['translations'])) {
            foreach ($b['translations'] as $locale => $fields) {
                if (!isset($mergedTranslations[$locale])) {
                    $mergedTranslations[$locale] = [];
                }
                foreach ($fields as $key => $value) {
                    $mergedTranslations[$locale][$key] = $value;
                }
            }
        }

        $merged = [
            'name'         => $b['name']      ?? ($existing['name'] ?? $code),
            'enabled'      => $b['enabled']   ?? ($existing['enabled'] ?? true),
            'exclusive'    => $b['exclusive'] ?? ($existing['exclusive'] ?? false),
            'priority'     => $b['priority']  ?? ($existing['priority'] ?? 0),
            'channels'     => $b['channels']  ?? ($existing['channels'] ?? []),
            'translations' => $mergedTranslations,
            'scopes'       => array_key_exists('scopes', $b)  ? $b['scopes']  : $this->stripMeta($existing['scopes']  ?? []),
            'actions'      => array_key_exists('actions', $b) ? $b['actions'] : $this->stripMeta($existing['actions'] ?? []),
            'startDate'    => $b['startDate'] ?? ($existing['startDate'] ?? null),
            'endDate'      => $b['endDate']   ?? ($existing['endDate'] ?? null),
        ];

        return $this->client->put(sprintf('catalog-promotions/%s', $code), $merged);
    }

    /** @param array<int, array<string, mixed>> $items */
    private function stripMeta(array $items): array
    {
        return array_map(static function (array $item): array {
            unset($item['@id'], $item['@type'], $item['id']);
            return $item;
        }, $items);
    }
}

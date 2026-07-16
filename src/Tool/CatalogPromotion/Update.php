<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_catalog_promotion',
    description: <<<'DESC'
update_catalog_promotion — Updates a catalog promotion. Only provided fields are changed; omitted fields keep their current values.

REQUIRED: code (the catalog promotion code to update).
OPTIONAL: name, channels (array of channel IRIs from list_channels @id), label, description, localeCode, enabled, exclusive, priority, startDate, endDate.
OPTIONAL: scopes/actions (JSON strings — omit or pass '[]' to keep existing):
- scopes: '[{"type":"for_taxons","configuration":{"taxons":["TAXON_CODE"]}}]' or '[{"type":"for_variants","configuration":{"variants":["VARIANT_CODE"]}}]' or '[{"type":"for_products","configuration":{"products":["PRODUCT_CODE"]}}]'
- actions: '[{"type":"percentage_discount","configuration":{"amount":0.2}}]' (20% off) or '[{"type":"fixed_discount","configuration":{"CHANNEL_CODE":{"amount":1000}}}]' (10.00 off — ALL channels required)
DESC,
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client) {}

    /**
     * @param string[] $channels Array of channel IRIs (from list_channels @id).
     */
    public function __invoke(
        string $code,
        string $name = '',
        array $channels = [],
        string $scopes = '[]',
        string $actions = '[]',
        string $label = '',
        string $description = '',
        string $localeCode = 'en_US',
        ?bool $enabled = null,
        ?bool $exclusive = null,
        int $priority = -1,
        string $startDate = '',
        string $endDate = '',
    ): string {
        $existing = json_decode($this->client->get(sprintf('catalog-promotions/%s', $code)), true);

        $decodedScopes  = json_decode($scopes, true);
        $decodedActions = json_decode($actions, true);

        $translations = $existing['translations'] ?? [];
        if ($name !== '' || $label !== '' || $description !== '') {
            if (!isset($translations[$localeCode])) {
                $translations[$localeCode] = [];
            }
            if ($label !== '') {
                $translations[$localeCode]['label'] = $label;
            } elseif ($name !== '' && !isset($translations[$localeCode]['label'])) {
                $translations[$localeCode]['label'] = $name;
            }
            if ($description !== '') {
                $translations[$localeCode]['description'] = $description;
            }
        }

        $body = [
            'name'        => $name !== '' ? $name : ($existing['name'] ?? $code),
            'enabled'     => $enabled ?? ($existing['enabled'] ?? true),
            'exclusive'   => $exclusive ?? ($existing['exclusive'] ?? false),
            'priority'    => $priority >= 0 ? $priority : ($existing['priority'] ?? 0),
            'channels'    => $channels !== [] ? $channels : ($existing['channels'] ?? []),
            'scopes'      => ($decodedScopes !== null && $decodedScopes !== [])  ? $decodedScopes  : $this->stripMeta($existing['scopes']  ?? []),
            'actions'     => ($decodedActions !== null && $decodedActions !== []) ? $decodedActions : $this->stripMeta($existing['actions'] ?? []),
            'translations' => $translations,
            'startDate'   => $startDate !== '' ? $startDate : ($existing['startDate'] ?? null),
            'endDate'     => $endDate !== '' ? $endDate : ($existing['endDate'] ?? null),
        ];

        return $this->client->put(sprintf('catalog-promotions/%s', $code), $body);
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

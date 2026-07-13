<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_catalog_promotion',
    description: 'update_catalog_promotion(code, name, channelCodes, scopes, actions, label?, description?, localeCode?, enabled?, exclusive?, priority?, startDate?, endDate?) → JSON of the updated Sylius catalog promotion. Uses PUT. Fetches existing translations to avoid overwriting other locales.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $code         Catalog promotion code to update.
     * @param string   $name         Internal name.
     * @param string[] $channelCodes Channel codes (replaces existing).
     * @param array    $scopes       Scopes array (replaces existing).
     * @param array    $actions      Actions array (replaces existing).
     * @param string   $label        Customer-facing label for the given locale. Default = same as name.
     * @param string   $description  Customer-facing description. Default = "".
     * @param string   $localeCode   Locale for label/description. Default = "en_US".
     * @param bool     $enabled      Whether the promotion is active. Default = true.
     * @param bool     $exclusive    Exclusive flag. Default = false.
     * @param int      $priority     Priority. Default = 0.
     * @param string   $startDate    Start datetime ISO 8601. Default = "" (no start limit).
     * @param string   $endDate      End datetime ISO 8601. Default = "" (no end limit).
     */
    public function __invoke(
        string $code,
        string $name,
        array $channelCodes,
        array $scopes,
        array $actions,
        string $label = '',
        string $description = '',
        string $localeCode = 'en_US',
        bool $enabled = true,
        bool $exclusive = false,
        int $priority = 0,
        string $startDate = '',
        string $endDate = '',
    ): string {
        $existing = json_decode($this->client->get(sprintf('catalog-promotions/%s', $code)), true);
        $translations = $existing['translations'] ?? [];

        if (!isset($translations[$localeCode])) {
            $translations[$localeCode] = ['locale' => $localeCode];
        }
        $translations[$localeCode]['label'] = $label !== '' ? $label : $name;
        if ($description !== '') {
            $translations[$localeCode]['description'] = $description;
        }

        $body = [
            'name' => $name,
            'enabled' => $enabled,
            'exclusive' => $exclusive,
            'priority' => $priority,
            'channels' => array_map(
                static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c),
                $channelCodes,
            ),
            'scopes' => $scopes,
            'actions' => $actions,
            'translations' => $translations,
            'startDate' => $startDate !== '' ? $startDate : null,
            'endDate' => $endDate !== '' ? $endDate : null,
        ];

        return $this->client->put(sprintf('catalog-promotions/%s', $code), $body);
    }
}

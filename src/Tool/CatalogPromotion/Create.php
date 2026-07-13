<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_catalog_promotion',
    description: 'create_catalog_promotion(code, name, channelCodes, scopes, actions, label?, description?, localeCode?, enabled?, exclusive?, priority?, startDate?, endDate?) → JSON of the newly created Sylius catalog promotion. scopes example: [{"type":"for_taxons","configuration":{"taxons":["CAPS"]}}]. Scope types: "for_taxons", "for_variants", "for_products". actions example: [{"type":"percentage_discount","configuration":{"amount":0.1}}]. Action types: "percentage_discount" (amount=0.0-1.0), "fixed_discount" ({"CHANNEL_CODE":{"amount":500}}).',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $code         Unique catalog promotion code.
     * @param string   $name         Internal name (not customer-facing).
     * @param string[] $channelCodes Channel codes where this promotion is active.
     * @param array    $scopes       Array of scope objects with "type" and "configuration".
     * @param array    $actions      Array of action objects with "type" and "configuration".
     * @param string   $label        Customer-facing label for the given locale. Default = same as name.
     * @param string   $description  Customer-facing description. Default = "".
     * @param string   $localeCode   Locale for label/description translation. Default = "en_US".
     * @param bool     $enabled      Whether the promotion is active. Default = true.
     * @param bool     $exclusive    If true, other catalog promotions do not apply. Default = false.
     * @param int      $priority     Application priority (higher = applied first). Default = 0.
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
        $translation = ['locale' => $localeCode, 'label' => $label !== '' ? $label : $name];
        if ($description !== '') {
            $translation['description'] = $description;
        }

        $body = [
            'code' => $code,
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
            'translations' => [$localeCode => $translation],
        ];

        if ($startDate !== '') {
            $body['startDate'] = $startDate;
        }
        if ($endDate !== '') {
            $body['endDate'] = $endDate;
        }

        return $this->client->post('catalog-promotions', $body);
    }
}

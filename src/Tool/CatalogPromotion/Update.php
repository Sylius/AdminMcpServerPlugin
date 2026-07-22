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

namespace Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_catalog_promotion',
    description: <<<'DESC'
update_catalog_promotion(code, body) → JSON of the updated catalog promotion.

You can pass a partial body with only the fields you want to change — e.g. {"name":"New Name"} or {"enabled":false} works without fetching the full JSON first. To change scopes or actions, include only those arrays and the tool will merge/strip meta automatically. For translations with @id, fetch first to preserve the @id of existing locales.

scopes examples: [{"type":"for_taxons","configuration":{"taxons":["TAXON_CODE"]}}] or [{"type":"for_variants","configuration":{"variants":["VARIANT_CODE"]}}]
actions examples: [{"type":"percentage_discount","configuration":{"amount":0.2}}] (20% off) or [{"type":"fixed_discount","configuration":{"CHANNEL_CODE":{"amount":1000}}}] (ALL channels required)
Note: nested @id/@type/id in scopes/actions are stripped automatically (Sylius rejects them on PUT).
DESC,
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(string $code, string $body): string
    {
        $b = json_decode($body, true) ?? [];
        if (isset($b['scopes'])) {
            $b['scopes'] = $this->stripMeta($b['scopes']);
        }
        if (isset($b['actions'])) {
            $b['actions'] = $this->stripMeta($b['actions']);
        }

        return $this->client->put(sprintf('catalog-promotions/%s', $code), $b);
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

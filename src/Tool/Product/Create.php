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

namespace Sylius\AdminMcpServerPlugin\Tool\Product;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_product',
    description: <<<'DESC'
create_product — Creates a new product in the store catalog. IMPORTANT: run list_channels first to get channel IRIs — a product without channels won't appear in any shop.

REQUIRED: code (unique product identifier, no spaces, e.g. "BLUE_MUG_001"), name (default display name for en_US fallback).
RECOMMENDED: channels (array of channel IRIs from list_channels @id, e.g. ["/api/v2/admin/channels/FASHION_WEB"]).
OPTIONAL: translations (JSON map of locale → {name, slug?, description?, shortDescription?} — provide all languages at once, e.g. '{"en_US":{"name":"Blue Mug","slug":"blue-mug"},"pl_PL":{"name":"Niebieski kubek","slug":"niebieski-kubek"}}'), enabled (default true). Slug is auto-generated if omitted.

After creating a product:
1. Call create_product_variant with a price (the product needs at least one variant to be purchasable).
2. Call create_product_taxon to assign the product to a category (taxon).

If user only provides a name, suggest a code (uppercase with underscores from name), ask about channels, and proceed with defaults for the rest.
DESC,
)]
final readonly class Create
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    /**
     * @param string   $translations JSON map of locale → {name, slug?, description?, shortDescription?}.
     * @param string[] $channels     Channel IRIs (from list_channels @id).
     */
    public function __invoke(
        string $code,
        string $name,
        string $translations = '{}',
        bool $enabled = true,
        array $channels = [],
    ): string {
        $decodedTranslations = json_decode($translations, true);
        if (empty($decodedTranslations)) {
            $decodedTranslations = ['en_US' => ['name' => $name]];
        }

        $body = [
            'code' => $code,
            'enabled' => $enabled,
            'translations' => $decodedTranslations,
        ];

        if ($channels !== []) {
            $body['channels'] = $channels;
        }

        return $this->client->post('products', $body);
    }
}

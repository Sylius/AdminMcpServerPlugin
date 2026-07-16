<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Product;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product',
    description: <<<'DESC'
update_product(code, translations?, enabled?, channels?) → JSON object of the updated Sylius product. Only provided fields are changed.

translations (JSON string) — map of locale → translation fields (name, slug, description, shortDescription). Pass multiple locales at once:
'{"en_US": {"name": "My Product", "slug": "my-product", "description": "A great product"}, "pl_PL": {"name": "Mój produkt", "slug": "moj-produkt"}}'
NOTE: slug does NOT auto-update when you change the name; update it separately if needed.

channels: array of channel IRIs from list_channels @id (replaces existing). Omit to keep current.
NOTE: to assign/remove product categories (taxons) use create_product_taxon / delete_product_taxon instead.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string    $code         Product code to update.
     * @param string    $translations JSON map of locale → {name?, slug?, description?, shortDescription?}.
     * @param bool|null $enabled      Set enabled status. Null = do not change.
     * @param string[]  $channels     Array of channel IRIs (from list_channels @id) — replaces existing. Empty = do not change.
     */
    public function __invoke(
        string $code,
        string $translations = '{}',
        ?bool $enabled = null,
        array $channels = [],
    ): string {
        $body = [];

        $incoming = json_decode($translations, true);
        if (!empty($incoming)) {
            $existing = json_decode($this->client->get(sprintf('products/%s', $code)), true);
            $merged = $existing['translations'] ?? [];
            foreach ($incoming as $locale => $fields) {
                if (!isset($merged[$locale])) {
                    $merged[$locale] = [];
                }
                foreach ($fields as $key => $value) {
                    $merged[$locale][$key] = $value;
                }
            }
            $body['translations'] = $merged;
        }

        if ($enabled !== null) {
            $body['enabled'] = $enabled;
        }
        if ($channels !== []) {
            $body['channels'] = $channels;
        }

        return $this->client->put(sprintf('products/%s', $code), $body);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Product;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product',
    description: <<<'DESC'
update_product(code, body) → JSON of the updated Sylius product. Only fields in body are changed.

body (JSON string) — fields: enabled (bool), channels (array of channel IRIs from list_channels @id), translations (map of locale → {name?, slug?, description?, shortDescription?}).
NOTE: slug does NOT auto-update when you change the name; update it separately if needed.
NOTE: to assign/remove product categories (taxons) use create_product_taxon / delete_product_taxon instead.
Example: '{"enabled":true,"translations":{"en_US":{"name":"My Product","slug":"my-product"},"pl_PL":{"name":"Mój produkt","slug":"moj-produkt"}}}'
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
        $b = json_decode($body, true) ?? [];
        $result = [];

        if (isset($b['translations'])) {
            $existing = json_decode($this->client->get(sprintf('products/%s', $code)), true);
            $merged = $existing['translations'] ?? [];
            foreach ($b['translations'] as $locale => $fields) {
                if (!isset($merged[$locale])) {
                    $merged[$locale] = [];
                }
                foreach ($fields as $key => $value) {
                    $merged[$locale][$key] = $value;
                }
            }
            $result['translations'] = $merged;
        }

        if (isset($b['enabled'])) {
            $result['enabled'] = $b['enabled'];
        }
        if (isset($b['channels'])) {
            $result['channels'] = $b['channels'];
        }

        return $this->client->put(sprintf('products/%s', $code), $result);
    }
}

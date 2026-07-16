<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociationType;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_association_type',
    description: <<<'DESC'
update_product_association_type(code, body) → JSON of the updated product association type. Only fields in body are changed.

body (JSON string) — fields: translations (map of locale → {name?}).
Example: '{"translations":{"en_US":{"name":"Similar Products"},"pl_PL":{"name":"Podobne Produkty"}}}'
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
        $existing = json_decode($this->client->get(sprintf('product-association-types/%s', $code)), true);
        $translations = $existing['translations'] ?? [];

        foreach ($b['translations'] ?? [] as $locale => $fields) {
            if (!isset($translations[$locale])) {
                $translations[$locale] = [];
            }
            foreach ($fields as $key => $value) {
                $translations[$locale][$key] = $value;
            }
        }

        return $this->client->put(sprintf('product-association-types/%s', $code), ['translations' => $translations]);
    }
}

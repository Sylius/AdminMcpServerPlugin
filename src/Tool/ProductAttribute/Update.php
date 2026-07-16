<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAttribute;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_attribute',
    description: <<<'DESC'
update_product_attribute(code, body) → JSON of the updated product attribute. Only fields in body are changed.

body (JSON string) — fields: translations (map of locale → {name?}).
Example: '{"translations":{"en_US":{"name":"Material"},"pl_PL":{"name":"Materiał"}}}'
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
        $existing = json_decode($this->client->get(sprintf('product-attributes/%s', $code)), true);
        $translations = $existing['translations'] ?? [];

        foreach ($b['translations'] ?? [] as $locale => $fields) {
            if (!isset($translations[$locale])) {
                $translations[$locale] = [];
            }
            foreach ($fields as $key => $value) {
                $translations[$locale][$key] = $value;
            }
        }

        return $this->client->put(sprintf('product-attributes/%s', $code), ['translations' => $translations]);
    }
}

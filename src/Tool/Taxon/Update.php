<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Taxon;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_taxon',
    description: <<<'DESC'
update_taxon(code, body) → JSON of the updated taxon (product category). Only fields in body are changed. Existing translations in other locales are always preserved.

body (JSON string) — fields: enabled (bool), parent (IRI from list_taxons @id — moves to a different parent category), translations (map of locale → {name?, slug?}).
NOTE: slug does NOT auto-update when you change the name; update it separately if needed.
Example: '{"enabled":true,"translations":{"en_US":{"name":"T-Shirts","slug":"t-shirts"},"pl_PL":{"name":"Koszulki","slug":"koszulki"}}}'
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
            $existing = json_decode($this->client->get(sprintf('taxons/%s', $code)), true);
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
        if (isset($b['parent'])) {
            $result['parent'] = $b['parent'];
        }

        return $this->client->put(sprintf('taxons/%s', $code), $result);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Taxon;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_taxon',
    description: <<<'DESC'
update_taxon — Updates a product category. All fields are optional — only provided values change. Existing translations in other locales are always preserved.

translations (JSON string) — map of locale → translation fields (name, slug). Pass multiple locales at once:
'{"en_US": {"name": "T-Shirts", "slug": "t-shirts"}, "pl_PL": {"name": "Koszulki", "slug": "koszulki"}}'
NOTE: slug does NOT auto-update when you change the name; update it separately if needed.

enabled (true=visible in shop, false=hidden), parent (move to a different parent category — IRI from list_taxons @id, e.g. "/api/v2/admin/taxons/CLOTHING").
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string    $code         Taxon code to update.
     * @param string    $translations JSON map of locale → {name?, slug?}.
     * @param bool|null $enabled      Set enabled status. Null = do not change.
     * @param string    $parent       New parent taxon IRI from list_taxons @id. Leave empty to keep current parent.
     */
    public function __invoke(
        string $code,
        string $translations = '{}',
        ?bool $enabled = null,
        string $parent = '',
    ): string {
        $body = [];

        $incoming = json_decode($translations, true);
        if (!empty($incoming)) {
            $existing = json_decode($this->client->get(sprintf('taxons/%s', $code)), true);
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
        if ($parent !== '') {
            $body['parent'] = $parent;
        }

        return $this->client->put(sprintf('taxons/%s', $code), $body);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Taxon;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_taxon',
    description: <<<'DESC'
update_taxon — Updates a product category. All fields are optional — only provided values change. Existing translations in other locales are always preserved.

FIELDS: name (display name), slug (URL path — NOTE: slug does NOT auto-update when you change the name; update it separately if needed), enabled (true=visible in shop, false=hidden), parentCode (move to a different parent category), localeCode (default "en_US" — use "fr_FR" etc. to add/update translations in another language).

To rename a category and keep the URL in sync, pass both name and slug in the same call.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string    $code        Taxon code to update.
     * @param string    $name        New taxon name for the given locale.
     * @param string    $slug        New URL slug for the given locale.
     * @param string    $localeCode  Locale code for the translation. Default = "en_US".
     * @param bool|null $enabled     Set enabled status (null = do not change).
     * @param string    $parentCode  New parent taxon code. Leave empty to keep current parent.
     */
    public function __invoke(
        string $code,
        string $name = '',
        string $slug = '',
        string $localeCode = 'en_US',
        ?bool $enabled = null,
        string $parentCode = '',
    ): string {
        $existing = json_decode($this->client->get(sprintf('taxons/%s', $code)), true);

        $body = [];

        if ($enabled !== null) {
            $body['enabled'] = $enabled;
        }

        if ($parentCode !== '') {
            $body['parent'] = $this->client->iri(sprintf('taxons/%s', $this->client->normalizeCode($parentCode)));
        }

        $hasTranslationFields = $name !== '' || $slug !== '';
        if ($hasTranslationFields) {
            $translations = $existing['translations'] ?? [];

            if (!isset($translations[$localeCode])) {
                $translations[$localeCode] = ['locale' => $localeCode];
            }

            if ($name !== '') {
                $translations[$localeCode]['name'] = $name;
            }
            if ($slug !== '') {
                $translations[$localeCode]['slug'] = $slug;
            }

            $body['translations'] = $translations;
        }

        return $this->client->put(sprintf('taxons/%s', $code), $body);
    }
}

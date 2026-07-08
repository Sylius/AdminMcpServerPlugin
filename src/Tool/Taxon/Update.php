<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Taxon;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_taxon',
    description: 'update_taxon(code, name?, slug?, localeCode?, enabled?, parentCode?) → JSON object of the updated Sylius taxon. Only provided fields are changed.',
)]
final readonly class Update
{
    public function __construct(
        private AdminApiClient $client,
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
        $body = [];

        if ($enabled !== null) {
            $body['enabled'] = $enabled;
        }

        if ($parentCode !== '') {
            $body['parent'] = sprintf('/api/v2/admin/taxons/%s', $parentCode);
        }

        $hasTranslationFields = $name !== '' || $slug !== '';
        if ($hasTranslationFields) {
            $translation = [
                '@id' => sprintf('/api/v2/admin/taxon/%s/translations/%s', $code, $localeCode),
                'locale' => $localeCode,
            ];
            if ($name !== '') {
                $translation['name'] = $name;
            }
            if ($slug !== '') {
                $translation['slug'] = $slug;
            }

            $body['translations'] = [$localeCode => $translation];
        }

        return $this->client->put(sprintf('taxons/%s', $code), $body);
    }
}

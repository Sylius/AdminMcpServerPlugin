<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Taxon;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_taxon',
    description: 'create_taxon(code, name, slug, localeCode?, enabled?, parentCode?) → JSON object of the newly created Sylius taxon. parentCode is the code of the parent taxon (e.g. "category"). Leave empty to create a root taxon.',
)]
final readonly class Create
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code        Unique taxon code (e.g. "my_category").
     * @param string $name        Taxon name for the given locale.
     * @param string $slug        URL slug for the given locale (e.g. "my-category").
     * @param string $localeCode  Locale code for the translation. Default = "en_US".
     * @param bool   $enabled     Whether the taxon is enabled. Default = true.
     * @param string $parentCode  Code of the parent taxon. Leave empty for root taxon.
     */
    public function __invoke(
        string $code,
        string $name,
        string $slug,
        string $localeCode = 'en_US',
        bool $enabled = true,
        string $parentCode = '',
    ): string {
        $body = [
            'code' => $code,
            'enabled' => $enabled,
            'translations' => [
                $localeCode => [
                    'name' => $name,
                    'slug' => $slug,
                    'locale' => $localeCode,
                ],
            ],
        ];

        if ($parentCode !== '') {
            $body['parent'] = sprintf('/api/v2/admin/taxons/%s', $parentCode);
        }

        return $this->client->post('taxons', $body);
    }
}

<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Product;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product',
    description: 'update_product(code, name?, slug?, localeCode?, description?, shortDescription?, enabled?, channels?) → JSON object of the updated Sylius product. Only provided fields are changed. channels replaces the full channel list.',
)]
final readonly class Update
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string    $code             Product code to update.
     * @param string    $name             New product name for the given locale.
     * @param string    $slug             New URL slug for the given locale.
     * @param string    $localeCode       Locale code for the translation. Default = "en_US".
     * @param string    $description      New full description.
     * @param string    $shortDescription New short description.
     * @param bool|null $enabled          Set enabled status (null = do not change).
     * @param string[]  $channels         New list of channel codes (replaces existing).
     */
    public function __invoke(
        string $code,
        string $name = '',
        string $slug = '',
        string $localeCode = 'en_US',
        string $description = '',
        string $shortDescription = '',
        ?bool $enabled = null,
        array $channels = [],
    ): string {
        $body = [];

        if ($enabled !== null) {
            $body['enabled'] = $enabled;
        }

        $hasTranslationFields = $name !== '' || $slug !== '' || $description !== '' || $shortDescription !== '';
        if ($hasTranslationFields) {
            $translation = [
                '@id' => sprintf('/api/v2/admin/products/%s/translations/%s', $code, $localeCode),
                'locale' => $localeCode,
            ];
            if ($name !== '') {
                $translation['name'] = $name;
            }
            if ($slug !== '') {
                $translation['slug'] = $slug;
            }
            if ($description !== '') {
                $translation['description'] = $description;
            }
            if ($shortDescription !== '') {
                $translation['shortDescription'] = $shortDescription;
            }

            $body['translations'] = [$localeCode => $translation];
        }

        if ($channels !== []) {
            $body['channels'] = array_map(
                static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c),
                $channels,
            );
        }

        return $this->client->put(sprintf('products/%s', $code), $body);
    }
}

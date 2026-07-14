<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Product;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product',
    description: 'update_product(code, name?, slug?, localeCode?, description?, shortDescription?, enabled?, channels?) → JSON object of the updated Sylius product. Only provided fields are changed. channels: if provided (non-empty array), replaces all channel assignments — omit or leave empty to keep existing channels.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
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
     * @param string[]  $channels         Array of channel IRIs (from list_channels @id) — replaces existing.
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
            $existing = json_decode($this->client->get(sprintf('products/%s', $code)), true);
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
            if ($description !== '') {
                $translations[$localeCode]['description'] = $description;
            }
            if ($shortDescription !== '') {
                $translations[$localeCode]['shortDescription'] = $shortDescription;
            }

            $body['translations'] = $translations;
        }

        if ($channels !== []) {
            $body['channels'] = $channels;
        }

        return $this->client->put(sprintf('products/%s', $code), $body);
    }
}

<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Product;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_product',
    description: 'create_product(code, name, slug, localeCode?, description?, shortDescription?, enabled?, channels?) → JSON object of the newly created Sylius product. Translations are created for the given locale. channels is a list of channel codes (e.g. ["FASHION_WEB"]).',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $code             Unique product code (e.g. "MUG_BLUE").
     * @param string   $name             Product name for the given locale.
     * @param string   $slug             URL slug for the given locale (e.g. "blue-mug").
     * @param string   $localeCode       Locale code for the translation. Default = "en_US".
     * @param string   $description      Full description. Default = "".
     * @param string   $shortDescription Short description. Default = "".
     * @param bool     $enabled          Whether the product is enabled. Default = true.
     * @param string[] $channels         List of channel codes to assign (e.g. ["FASHION_WEB"]).
     */
    public function __invoke(
        string $code,
        string $name,
        string $slug,
        string $localeCode = 'en_US',
        string $description = '',
        string $shortDescription = '',
        bool $enabled = true,
        array $channels = [],
    ): string {
        $translation = [
            'name' => $name,
            'slug' => $slug,
            'locale' => $localeCode,
        ];

        if ($description !== '') {
            $translation['description'] = $description;
        }

        if ($shortDescription !== '') {
            $translation['shortDescription'] = $shortDescription;
        }

        $body = [
            'code' => $code,
            'enabled' => $enabled,
            'translations' => [
                $localeCode => $translation,
            ],
        ];

        if ($channels !== []) {
            $body['channels'] = array_map(
                static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c),
                $channels,
            );
        }

        return $this->client->post('products', $body);
    }
}

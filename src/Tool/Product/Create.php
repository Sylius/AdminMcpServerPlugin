<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Product;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_product',
    description: 'create_product(code, name, localeCode?, slug?, description?, shortDescription?, enabled?, channels?) → JSON object of the newly created Sylius product. slug is auto-generated from name if omitted. IMPORTANT: assign channels (use list_channels to find codes) — without channels the product will not appear in any shop.',
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
     * @param string   $localeCode       Locale code for the translation. Default = "en_US".
     * @param string   $slug             URL slug for the given locale (e.g. "blue-mug"). Auto-generated from name if empty.
     * @param string   $description      Full description. Default = "".
     * @param string   $shortDescription Short description. Default = "".
     * @param bool     $enabled          Whether the product is enabled. Default = true.
     * @param string[] $channels         List of channel codes to assign (e.g. ["FASHION_WEB"]). Use list_channels to get available codes.
     */
    public function __invoke(
        string $code,
        string $name,
        string $localeCode = 'en_US',
        string $slug = '',
        string $description = '',
        string $shortDescription = '',
        bool $enabled = true,
        array $channels = [],
    ): string {
        $translation = [
            'name' => $name,
            'locale' => $localeCode,
        ];

        if ($slug !== '') {
            $translation['slug'] = $slug;
        }

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

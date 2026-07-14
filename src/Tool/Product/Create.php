<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Product;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_product',
    description: <<<'DESC'
create_product — Creates a new product in the store catalog. IMPORTANT: run list_channels first to get channel IRIs — a product without channels won't appear in any shop.

REQUIRED: code (unique product identifier, no spaces, e.g. "BLUE_MUG_001"), name (product display name).
RECOMMENDED: channels (array of channel IRIs from list_channels @id, e.g. ["/api/v2/admin/channels/FASHION_WEB"]).
OPTIONAL: description, shortDescription, enabled (default true), localeCode (default "en_US"), slug (URL path, auto-generated from name if omitted).

If user only provides a name, suggest a code (uppercase with underscores from name), ask about channels, and proceed with defaults for the rest. Slug is auto-generated so never ask for it unless user wants a specific URL.
DESC,
)]
final readonly class Create
{
    public function __construct(private ApiClientInterface $client) {}

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
        $translation = ['name' => $name, 'locale' => $localeCode];
        if ($slug !== '') { $translation['slug'] = $slug; }
        if ($description !== '') { $translation['description'] = $description; }
        if ($shortDescription !== '') { $translation['shortDescription'] = $shortDescription; }

        $body = [
            'code'         => $code,
            'enabled'      => $enabled,
            'translations' => [$localeCode => $translation],
        ];

        if ($channels !== []) {
            $body['channels'] = $channels;
        }

        return $this->client->post('products', $body);
    }
}

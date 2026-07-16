<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociationType;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_association_type',
    description: 'update_product_association_type(code, name, localeCode?) → JSON object of the updated Sylius product association type. Updates the translation name for the given locale.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code       Product association type code to update.
     * @param string $name       New display name for the given locale.
     * @param string $localeCode Locale for the translation. Default = "en_US".
     */
    public function __invoke(string $code, string $name, string $localeCode = 'en_US'): string
    {
        $existing = json_decode($this->client->get(sprintf('product-association-types/%s', $code)), true);
        $translations = $existing['translations'] ?? [];
        if (isset($translations[$localeCode])) {
            $translations[$localeCode]['name'] = $name;
        } else {
            $translations[$localeCode] = ['name' => $name];
        }

        return $this->client->put(sprintf('product-association-types/%s', $code), ['translations' => $translations]);
    }
}

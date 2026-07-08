<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductAttribute;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_attribute',
    description: 'update_product_attribute(code, name, localeCode?) → JSON object of the updated Sylius product attribute. Updates the translation name for the given locale.',
)]
final readonly class Update
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code       Product attribute code to update.
     * @param string $name       New display name for the given locale.
     * @param string $localeCode Locale for the translation. Default = "en_US".
     */
    public function __invoke(string $code, string $name, string $localeCode = 'en_US'): string
    {
        return $this->client->put(sprintf('product-attributes/%s', $code), [
            'translations' => [
                $localeCode => [
                    '@id' => sprintf('/api/v2/admin/product-attributes/%s/translations/%s', $code, $localeCode),
                    'locale' => $localeCode,
                    'name' => $name,
                ],
            ],
        ]);
    }
}

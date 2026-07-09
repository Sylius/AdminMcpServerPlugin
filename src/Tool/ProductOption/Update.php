<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductOption;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_option',
    description: 'update_product_option(code, name, localeCode?) → JSON object of the updated Sylius product option. Updates the translation name for the given locale.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code       Product option code to update.
     * @param string $name       New display name for the given locale.
     * @param string $localeCode Locale for the translation. Default = "en_US".
     */
    public function __invoke(string $code, string $name, string $localeCode = 'en_US'): string
    {
        return $this->client->put(sprintf('product-options/%s', $code), [
            'translations' => [
                $localeCode => [
                    '@id' => sprintf('/api/v2/admin/product-options/%s/translations/%s', $code, $localeCode),
                    'locale' => $localeCode,
                    'name' => $name,
                ],
            ],
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductOption;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
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
                    '@id' => $this->client->iri(sprintf('product-options/%s/translations/%s', $code, $localeCode)),
                    'locale' => $localeCode,
                    'name' => $name,
                ],
            ],
        ]);
    }
}

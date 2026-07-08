<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductAssociationType;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_product_association_type',
    description: 'create_product_association_type(code, name, localeCode?) → JSON object of the newly created Sylius product association type. code must be unique.',
)]
final readonly class Create
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code       Unique association type code (e.g. "cross_sell").
     * @param string $name       Display name for the given locale.
     * @param string $localeCode Locale for the name translation. Default = "en_US".
     */
    public function __invoke(string $code, string $name, string $localeCode = 'en_US'): string
    {
        return $this->client->post('product-association-types', [
            'code' => $code,
            'translations' => [
                $localeCode => [
                    'locale' => $localeCode,
                    'name' => $name,
                ],
            ],
        ]);
    }
}

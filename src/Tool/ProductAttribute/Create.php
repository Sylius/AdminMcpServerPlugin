<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductAttribute;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_product_attribute',
    description: 'create_product_attribute(code, type, name, localeCode?) → JSON object of the newly created Sylius product attribute. type must be one of: text, integer, float, datetime, date, select, checkbox.',
)]
final readonly class Create
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code       Unique attribute code (e.g. "material").
     * @param string $type       Attribute type: text, integer, float, datetime, date, select, checkbox.
     * @param string $name       Attribute display name for the given locale.
     * @param string $localeCode Locale for the name translation. Default = "en_US".
     */
    public function __invoke(
        string $code,
        string $type,
        string $name,
        string $localeCode = 'en_US',
    ): string {
        return $this->client->post('product-attributes', [
            'code' => $code,
            'type' => $type,
            'translations' => [
                $localeCode => [
                    'locale' => $localeCode,
                    'name' => $name,
                ],
            ],
        ]);
    }
}

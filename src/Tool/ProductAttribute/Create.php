<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAttribute;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_product_attribute',
    description: <<<'DESC'
create_product_attribute — Creates a product attribute definition (e.g. "Material", "Brand", "Weight"). Attributes are metadata displayed on the product page.

REQUIRED: code (unique slug, e.g. "hat_material"), type, name (display label).
TYPE options: text=free text like "100%% cotton", integer=whole number, float=decimal like 1.5, checkbox=yes/no toggle, date=date only, datetime=date+time, select=predefined choices from a dropdown.

After creating an attribute definition, assign values to specific products using set_product_attribute_value. Use list_product_attributes to check if an attribute already exists before creating a new one.
DESC,
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
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

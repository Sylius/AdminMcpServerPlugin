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
    name: 'set_product_attribute_value',
    description: <<<'DESC'
set_product_attribute_value — Assigns (or updates) an attribute value on a product. If the attribute is already set for that locale it will be overwritten.

REQUIRED: productCode (product to update), attribute (attribute definition IRI, e.g. "/api/v2/admin/product-attributes/cap_brand" — get IRIs from list_product_attributes), value (the value to store, as a string — e.g. "100% cotton" for text type, "10.5" for float, "true"/"false" for checkbox).
OPTIONAL: localeCode (default "en_US").

NOTE: Product attributes are metadata about the product (material, brand, etc.) — not related to pricing or stock. Use list_product_attributes to see what attribute types exist before assigning values.
DESC,
)]
final readonly class SetValue
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    /**
     * @param string $attribute  Product attribute IRI (e.g. "/api/v2/admin/product-attributes/cap_brand").
     */
    public function __invoke(
        string $productCode,
        string $attribute,
        string $value,
        string $localeCode = 'en_US',
    ): string {
        $product = json_decode($this->client->get(sprintf('products/%s', $productCode)), true);
        $existingAttrs = $product['attributes'] ?? [];

        // Build the updated list: keep all existing, replace or append the target attribute+locale
        $updated = [];
        $found = false;

        foreach ($existingAttrs as $attr) {
            if (($attr['attribute'] ?? '') === $attribute && ($attr['localeCode'] ?? '') === $localeCode) {
                // Replace existing value
                $entry = ['attribute' => $attribute, 'localeCode' => $localeCode, 'value' => $value];
                if (isset($attr['@id'])) {
                    $entry['@id'] = $attr['@id'];
                }
                $updated[] = $entry;
                $found = true;
            } else {
                // Keep existing attribute as-is
                $entry = ['attribute' => $attr['attribute'], 'localeCode' => $attr['localeCode'], 'value' => $attr['value']];
                if (isset($attr['@id'])) {
                    $entry['@id'] = $attr['@id'];
                }
                $updated[] = $entry;
            }
        }

        if (!$found) {
            $updated[] = ['attribute' => $attribute, 'localeCode' => $localeCode, 'value' => $value];
        }

        $this->client->put(sprintf('products/%s', $productCode), ['attributes' => $updated]);

        return (string) json_encode([
            'productCode' => $productCode,
            'attribute' => $attribute,
            'localeCode' => $localeCode,
            'value' => $value,
            'updated' => true,
        ]);
    }
}

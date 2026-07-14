<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAttribute;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'set_product_attribute_value',
    description: <<<'DESC'
set_product_attribute_value — Assigns (or updates) an attribute value on a product. If the attribute is already set for that locale it will be overwritten.

REQUIRED: productCode (product to update), attributeCode (attribute definition code, e.g. "cap_brand" — get codes from list_product_attributes), value (the value to store, as a string — e.g. "100% cotton" for text type, "10.5" for float, "true"/"false" for checkbox).
OPTIONAL: localeCode (default "en_US").

NOTE: Product attributes are metadata about the product (material, brand, etc.) — not related to pricing or stock. Use list_product_attributes to see what attribute types exist before assigning values.
DESC,
)]
final readonly class SetValue
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(
        string $productCode,
        string $attributeCode,
        string $value,
        string $localeCode = 'en_US',
    ): string {
        $product = json_decode($this->client->get(sprintf('products/%s', $productCode)), true);
        $existingAttrs = $product['attributes'] ?? [];

        // Build the updated list: keep all existing, replace or append the target attribute+locale
        $attributeIri = sprintf('/api/v2/admin/product-attributes/%s', $attributeCode);
        $updated = [];
        $found = false;

        foreach ($existingAttrs as $attr) {
            if (($attr['attribute'] ?? '') === $attributeIri && ($attr['localeCode'] ?? '') === $localeCode) {
                // Replace existing value
                $entry = ['attribute' => $attributeIri, 'localeCode' => $localeCode, 'value' => $value];
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
            $updated[] = ['attribute' => $attributeIri, 'localeCode' => $localeCode, 'value' => $value];
        }

        $this->client->put(sprintf('products/%s', $productCode), ['attributes' => $updated]);

        return (string) json_encode([
            'productCode' => $productCode,
            'attributeCode' => $attributeCode,
            'localeCode' => $localeCode,
            'value' => $value,
            'updated' => true,
        ]);
    }
}

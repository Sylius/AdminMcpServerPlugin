<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAttribute;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'remove_product_attribute_value',
    description: <<<'DESC'
remove_product_attribute_value — Removes an attribute value from a product for a specific locale. If the attribute has values in other locales they are preserved.

REQUIRED: productCode, attributeCode (e.g. "cap_brand").
OPTIONAL: localeCode (default "en_US"). Pass localeCode="all" to remove the attribute for ALL locales at once.

Use get_product(productCode) to see current attribute values before removing them.
DESC,
)]
final readonly class RemoveValue
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(
        string $productCode,
        string $attributeCode,
        string $localeCode = 'en_US',
    ): string {
        $product = json_decode($this->client->get(sprintf('products/%s', $productCode)), true);
        $existingAttrs = $product['attributes'] ?? [];

        $attributeIri = $this->client->iri(sprintf('product-attributes/%s', $this->client->normalizeCode($attributeCode)));
        $removeAll = $localeCode === 'all';

        $updated = [];
        foreach ($existingAttrs as $attr) {
            $isTarget = ($attr['attribute'] ?? '') === $attributeIri
                && ($removeAll || ($attr['localeCode'] ?? '') === $localeCode);

            if (!$isTarget) {
                $entry = ['attribute' => $attr['attribute'], 'localeCode' => $attr['localeCode'], 'value' => $attr['value']];
                if (isset($attr['@id'])) {
                    $entry['@id'] = $attr['@id'];
                }
                $updated[] = $entry;
            }
        }

        $this->client->put(sprintf('products/%s', $productCode), ['attributes' => $updated]);

        return (string) json_encode([
            'productCode' => $productCode,
            'attributeCode' => $attributeCode,
            'localeCode' => $localeCode,
            'removed' => true,
        ]);
    }
}

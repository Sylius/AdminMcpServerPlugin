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
    name: 'remove_product_attribute_value',
    description: <<<'DESC'
remove_product_attribute_value — Removes an attribute value from a product for a specific locale. If the attribute has values in other locales they are preserved.

REQUIRED: productCode, attribute (attribute code e.g. "cap_brand" OR full IRI "/api/v2/admin/product-attributes/cap_brand" — both formats are accepted).
OPTIONAL: localeCode (default "en_US"). Pass localeCode="all" to remove the attribute for ALL locales at once.

Use get_product(productCode) to see current attribute values before removing them.
DESC,
)]
final readonly class RemoveValue
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
        string $localeCode = 'en_US',
    ): string {
        // Accept bare attribute code (e.g. "cap_brand") or full IRI
        if (!str_contains($attribute, '/')) {
            $attribute = sprintf('/api/v2/admin/product-attributes/%s', $attribute);
        }

        /** @var array<string, mixed> $product */
        $product = json_decode($this->client->get(sprintf('products/%s', $productCode)), true);
        /** @var array<int, array<string, mixed>> $existingAttrs */
        $existingAttrs = $product['attributes'] ?? [];

        $removeAll = $localeCode === 'all';

        $updated = [];
        foreach ($existingAttrs as $attr) {
            $isTarget = ($attr['attribute'] ?? '') === $attribute &&
                ($removeAll || ($attr['localeCode'] ?? '') === $localeCode);

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
            'attribute' => $attribute,
            'localeCode' => $localeCode,
            'removed' => true,
        ]);
    }
}

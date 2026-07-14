<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductOption;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'add_product_option_value',
    description: 'add_product_option_value(optionCode, valueCode, valueName, localeCode?) → Adds a new value to an existing product option (e.g. add "XL" to the "t_shirt_size" option). Existing values are preserved. Returns JSON of the updated option with all values.',
)]
final readonly class AddValue
{
    public function __construct(private ApiClientInterface $client) {}

    /**
     * @param string $optionCode  Product option code (e.g. "t_shirt_size").
     * @param string $valueCode   Unique value code (e.g. "t_shirt_size_xxl").
     * @param string $valueName   Display label for this value (e.g. "XXL").
     * @param string $localeCode  Locale for the translation. Default = "en_US".
     */
    public function __invoke(
        string $optionCode,
        string $valueCode,
        string $valueName,
        string $localeCode = 'en_US',
    ): string {
        $existing = json_decode($this->client->get(sprintf('product-options/%s', $optionCode)), true);

        // Collect existing values with @id so Sylius recognises them as existing (not new)
        $values = [];
        foreach ($existing['values'] ?? [] as $valueIri) {
            $code = basename((string) $valueIri);
            $valueData = json_decode(
                $this->client->get(sprintf('product-options/%s/values/%s', $optionCode, $code)),
                true,
            );
            $values[] = [
                '@id'          => $valueData['@id'] ?? $valueIri,
                'code'         => $code,
                'translations' => $valueData['translations'] ?? [],
            ];
        }

        // Add new value (avoid duplicates)
        $existing_codes = array_column($values, 'code');
        if (!in_array($valueCode, $existing_codes, true)) {
            $values[] = [
                'code'         => $valueCode,
                'translations' => [
                    $localeCode => ['locale' => $localeCode, 'value' => $valueName],
                ],
            ];
        }

        // Preserve existing translations
        $translations = $existing['translations'] ?? [];

        return $this->client->put(sprintf('product-options/%s', $optionCode), [
            'translations' => $translations,
            'values'       => $values,
        ]);
    }
}

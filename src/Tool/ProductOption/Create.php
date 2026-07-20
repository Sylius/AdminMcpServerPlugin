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

namespace Sylius\AdminMcpServerPlugin\Tool\ProductOption;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_product_option',
    description: <<<'DESC'
create_product_option — Creates a product option (a dimension like "Size" or "Color" used to create product variants). Options define what choices a customer makes when buying a product.

REQUIRED: code (e.g. "hat_size"), name (e.g. "Size").
OPTIONAL: valueCode + valueName to add the first option value immediately (e.g. valueCode="hat_size_sm", valueName="Small").

CONTEXT: Product options are shared across products. After creating an option, add more values using add_product_option_value(optionCode, valueCode, valueName). You can add the first value directly in this call via valueCode + valueName. Variants are then created with specific option value combinations.
DESC,
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code       Unique option code (e.g. "t_shirt_color").
     * @param string $name       Option display name for the given locale.
     * @param string $localeCode Locale for the translation. Default = "en_US".
     * @param string $valueCode  Optional first option value code (e.g. "t_shirt_color_red").
     * @param string $valueName  Optional first option value display text.
     */
    public function __invoke(
        string $code,
        string $name,
        string $localeCode = 'en_US',
        string $valueCode = '',
        string $valueName = '',
    ): string {
        $body = [
            'code' => $code,
            'translations' => [
                $localeCode => [
                    'locale' => $localeCode,
                    'name' => $name,
                ],
            ],
        ];

        if ($valueCode !== '' && $valueName !== '') {
            $body['values'] = [
                [
                    'code' => $valueCode,
                    'translations' => [
                        $localeCode => [
                            'locale' => $localeCode,
                            'value' => $valueName,
                        ],
                    ],
                ],
            ];
        }

        return $this->client->post('product-options', $body);
    }
}

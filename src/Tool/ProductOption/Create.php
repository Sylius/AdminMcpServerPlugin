<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductOption;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_product_option',
    description: 'create_product_option(code, name, localeCode?, valueCode?, valueName?) → JSON object of the newly created Sylius product option. Optionally creates a first option value.',
)]
final readonly class Create
{
    public function __construct(
        private AdminApiClient $client,
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

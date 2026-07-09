<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductVariant;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_product_variant',
    description: 'create_product_variant(code, productCode, channelCode?, price?, name?, localeCode?, onHand?, enabled?, tracked?) → JSON object of the newly created Sylius product variant. code and productCode are required.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code        Unique variant code (required).
     * @param string $productCode Parent product code (required).
     * @param string $channelCode Channel code for pricing. Default = "FASHION_WEB".
     * @param int    $price       Price in minor units (e.g. 999 = $9.99). Default = 0.
     * @param string $name        Variant display name for the given locale.
     * @param string $localeCode  Locale for the name translation. Default = "en_US".
     * @param int    $onHand      Stock quantity on hand. Default = 0.
     * @param bool   $enabled     Whether the variant is enabled. Default = true.
     * @param bool   $tracked     Whether stock is tracked. Default = false.
     */
    public function __invoke(
        string $code,
        string $productCode,
        string $channelCode = 'FASHION_WEB',
        int $price = 0,
        string $name = '',
        string $localeCode = 'en_US',
        int $onHand = 0,
        bool $enabled = true,
        bool $tracked = false,
    ): string {
        $body = [
            'code' => $code,
            'product' => sprintf('/api/v2/admin/products/%s', $productCode),
            'enabled' => $enabled,
            'tracked' => $tracked,
            'onHand' => $onHand,
            'channelPricings' => [
                $channelCode => ['price' => $price],
            ],
        ];

        if ($name !== '') {
            $body['translations'] = [
                $localeCode => [
                    'locale' => $localeCode,
                    'name' => $name,
                ],
            ];
        }

        return $this->client->post('product-variants', $body);
    }
}

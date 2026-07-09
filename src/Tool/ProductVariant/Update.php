<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductVariant;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_product_variant',
    description: 'update_product_variant(code, name?, localeCode?, onHand?, enabled?, tracked?, price?, channelCode?) → JSON object of the updated Sylius product variant. Uses PUT — only provided translation fields change; non-translation fields use their current API defaults if omitted.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string    $code        Variant code to update.
     * @param string    $name        New variant name for the given locale.
     * @param string    $localeCode  Locale for the name translation. Default = "en_US".
     * @param int|null  $onHand      New stock quantity on hand. Null = do not change.
     * @param bool|null $enabled     Set enabled status. Null = do not change.
     * @param bool|null $tracked     Set stock tracking. Null = do not change.
     * @param int|null  $price       New price in minor units. Null = do not change.
     * @param string    $channelCode Channel code for price update. Default = "FASHION_WEB".
     */
    public function __invoke(
        string $code,
        string $name = '',
        string $localeCode = 'en_US',
        ?int $onHand = null,
        ?bool $enabled = null,
        ?bool $tracked = null,
        ?int $price = null,
        string $channelCode = 'FASHION_WEB',
    ): string {
        $body = [];

        if ($enabled !== null) {
            $body['enabled'] = $enabled;
        }
        if ($tracked !== null) {
            $body['tracked'] = $tracked;
        }
        if ($onHand !== null) {
            $body['onHand'] = $onHand;
        }
        if ($price !== null) {
            $body['channelPricings'] = [
                $channelCode => [
                    '@id' => sprintf('/api/v2/admin/product-variants/%s/channel-pricings/%s', $code, $channelCode),
                    'price' => $price,
                ],
            ];
        }
        if ($name !== '') {
            $body['translations'] = [
                $localeCode => [
                    '@id' => sprintf('/api/v2/admin/product-variants/%s/translations/%s', $code, $localeCode),
                    'locale' => $localeCode,
                    'name' => $name,
                ],
            ];
        }

        return $this->client->put(sprintf('product-variants/%s', $code), $body);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductVariant;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_product_variants',
    description: 'list_product_variants(page?, itemsPerPage?, productCode?) → JSON Hydra collection of Sylius product variants. Each variant has: code, product, enabled, onHand, onHold, tracked, channelPricings, optionValues, translations (name per locale). TIP: Always pass productCode to filter by product — without it, all variants across the entire store are returned (can be hundreds).',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int    $page         Page number (1-based). Default = 1.
     * @param int    $itemsPerPage Items per page. Default = 30.
     * @param string $productCode  Filter by product code. Default = "" (all variants).
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30, string $productCode = ''): string
    {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        if ($productCode !== '') {
            $params['product'] = $this->client->iri(sprintf('products/%s', $productCode));
        }

        return $this->client->get('product-variants', $params);
    }
}

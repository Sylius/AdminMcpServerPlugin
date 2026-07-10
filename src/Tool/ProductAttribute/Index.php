<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAttribute;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_product_attributes',
    description: 'list_product_attributes(page?, itemsPerPage?) → JSON Hydra collection of Sylius product attributes. Each attribute has: code, type (text/integer/float/datetime/date/select/checkbox), translations (name per locale), position, translatable.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $page         Page number (1-based). Default = 1.
     * @param int $itemsPerPage Items per page. Default = 30.
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30): string
    {
        return $this->client->get('product-attributes', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingCategory;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_shipping_categories',
    description: 'list_shipping_categories(page?, itemsPerPage?) → JSON Hydra collection of Sylius shipping categories. Each item has: id, code, name, description. Use category codes when creating/updating shipping methods.',
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
        return $this->client->get('shipping-categories', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

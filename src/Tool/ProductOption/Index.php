<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductOption;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_product_options',
    description: 'list_product_options(page?, itemsPerPage?) → JSON Hydra collection of Sylius product options. Each option has: code, position, values (list of IRIs), translations (name per locale).',
)]
final readonly class Index
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param int $page         Page number (1-based). Default = 1.
     * @param int $itemsPerPage Items per page. Default = 30.
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30): string
    {
        return $this->client->get('product-options', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

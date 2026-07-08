<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Product;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_products',
    description: 'list_products(page?, itemsPerPage?) → JSON Hydra collection of Sylius products. Each product has: id, code, enabled, channels, mainTaxon, translations (name, slug, description per locale), variants, createdAt, updatedAt.',
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
        return $this->client->get('products', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\TaxCategory;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_tax_categories',
    description: 'list_tax_categories(page?, itemsPerPage?) → JSON Hydra collection of Sylius tax categories. Each category has: id, code, name, description, createdAt, updatedAt.',
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
        return $this->client->get('tax-categories', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

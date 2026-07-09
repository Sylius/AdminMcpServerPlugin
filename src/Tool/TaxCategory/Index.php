<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\TaxCategory;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_tax_categories',
    description: 'list_tax_categories(page?, itemsPerPage?) → JSON Hydra collection of Sylius tax categories. Each category has: id, code, name, description, createdAt, updatedAt.',
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
        return $this->client->get('tax-categories', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

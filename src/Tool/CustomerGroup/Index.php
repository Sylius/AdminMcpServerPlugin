<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\CustomerGroup;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_customer_groups',
    description: 'list_customer_groups(page?, itemsPerPage?) → JSON Hydra collection of Sylius customer groups. Each group has: id, code, name.',
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
        return $this->client->get('customer-groups', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

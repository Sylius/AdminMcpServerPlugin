<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Administrator;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_administrators',
    description: 'list_administrators(page?, itemsPerPage?) → JSON Hydra collection of Sylius admin users. Each administrator has: id, username, email, firstName, lastName, localeCode, enabled, lastLogin, createdAt, updatedAt.',
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
        return $this->client->get('administrators', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

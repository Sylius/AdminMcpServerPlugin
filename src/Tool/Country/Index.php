<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Country;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_countries',
    description: 'list_countries(page?, itemsPerPage?) → JSON Hydra collection of Sylius countries. Each country has: id, code (ISO 3166-1 alpha-2, e.g. "US", "PL", "DE"), enabled, provinces.',
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
        return $this->client->get('countries', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Zone;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_zones',
    description: 'list_zones(page?, itemsPerPage?) → Lists delivery/tax zones. Each zone has: code (use this as zoneCode in shipping methods), name, type (country=members are countries / zone=nested zones / province=members are provinces), scope (shipping/tax/all), members (list of country/province/zone codes). Use get_zone(code) to see member codes.',
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
        return $this->client->get('zones', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

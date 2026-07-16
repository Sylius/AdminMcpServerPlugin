<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ZoneMember;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_zone_members',
    description: 'list_zone_members(zoneCode, page?, itemsPerPage?) → JSON Hydra collection of members belonging to a zone. Each member has: @id (IRI), code (country ISO, province code, or zone code depending on the zone type). Use zoneCode from list_zones.',
)]
final readonly class Index
{
    public function __construct(private ApiClientInterface $client) {}

    /**
     * @param string $zoneCode    Zone code to filter by (e.g. "WORLD").
     * @param int    $page        Page number. Default = 1.
     * @param int    $itemsPerPage Items per page. Default = 30.
     */
    public function __invoke(string $zoneCode, int $page = 1, int $itemsPerPage = 30): string
    {
        return $this->client->get('zone-members', [
            'belongsTo.code' => $zoneCode,
            'page'           => $page,
            'itemsPerPage'   => $itemsPerPage,
        ]);
    }
}

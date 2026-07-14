<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ZoneMember;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_zone_members',
    description: 'list_zone_members(zoneCode) → Lists all members of a zone. Each member has a code (country ISO, province code, or zone code depending on the zone type). Equivalent to calling get_zone and reading the members array.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $zoneCode Zone code whose members to list (e.g. "WORLD").
     */
    public function __invoke(string $zoneCode): string
    {
        $zone = json_decode($this->client->get(sprintf('zones/%s', $zoneCode)), true);
        $members = $zone['members'] ?? [];

        return (string) json_encode([
            'zoneCode' => $zoneCode,
            'members'  => array_map(static fn (array $m) => $m['code'] ?? $m, $members),
            'total'    => count($members),
        ]);
    }
}

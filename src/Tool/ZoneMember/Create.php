<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ZoneMember;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'add_zone_member',
    description: 'add_zone_member(zoneCode, memberCode) → Adds a country, province, or zone as a member of a Sylius zone. Returns JSON of the created zone member with its numeric id (use for remove_zone_member). memberCode is the country ISO code (e.g. "US"), province code (e.g. "US-CA"), or zone code (e.g. "EU").',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $zoneCode   Zone code to add the member to (e.g. "WORLD").
     * @param string $memberCode Member code: country ISO (e.g. "US"), province code (e.g. "US-CA"), or zone code.
     */
    public function __invoke(string $zoneCode, string $memberCode): string
    {
        return $this->client->post('zone-members', [
            'code' => $memberCode,
            'belongsTo' => sprintf('/api/v2/admin/zones/%s', $zoneCode),
        ]);
    }
}

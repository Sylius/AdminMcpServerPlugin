<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ZoneMember;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'remove_zone_member',
    description: 'remove_zone_member(memberId) → Removes a member from a Sylius zone by the zone member\'s numeric ID. The numeric ID is returned by add_zone_member or visible in get_zone members array (last segment of the IRI, e.g. "/api/v2/admin/zone-members/5" → id=5). Returns empty string on success (HTTP 204).',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $memberId Numeric zone member ID (from add_zone_member response or get_zone members IRI).
     */
    public function __invoke(int $memberId): string
    {
        return $this->client->delete(sprintf('zone-members/%d', $memberId));
    }
}

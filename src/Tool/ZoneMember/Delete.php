<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ZoneMember;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'remove_zone_member',
    description: 'remove_zone_member(zoneCode, memberCode) → Removes a member from a Sylius zone by its code. Returns empty string on success.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $zoneCode   Zone code the member belongs to (e.g. "WORLD").
     * @param string $memberCode Member code to remove: country ISO (e.g. "US"), province code (e.g. "US-CA"), or zone code.
     */
    public function __invoke(string $zoneCode, string $memberCode): string
    {
        $zone = json_decode($this->client->get(sprintf('zones/%s', $zoneCode)), true);

        $members = array_values(array_filter(
            $zone['members'] ?? [],
            static fn (array $m) => ($m['code'] ?? '') !== $memberCode,
        ));

        $this->client->put(sprintf('zones/%s', $zoneCode), [
            'name'    => $zone['name'],
            'type'    => $zone['type'],
            'scope'   => $zone['scope'] ?? 'all',
            'members' => $members,
        ]);

        return '';
    }
}

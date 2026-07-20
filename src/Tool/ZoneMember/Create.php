<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ZoneMember;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'add_zone_member',
    description: 'add_zone_member(zoneCode, memberCode) → Adds a country, province, or zone as a member of a Sylius zone. Returns JSON confirming the addition. memberCode is the country ISO code (e.g. "US"), province code (e.g. "US-CA"), or zone code (e.g. "EU").',
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
        $zone = json_decode($this->client->get(sprintf('zones/%s', $zoneCode)), true);

        $members = $zone['members'] ?? [];
        $members[] = ['code' => $memberCode];

        $this->client->put(sprintf('zones/%s', $zoneCode), [
            'name' => $zone['name'],
            'type' => $zone['type'],
            'scope' => $zone['scope'] ?? 'all',
            'members' => $members,
        ]);

        return (string) json_encode(['zoneCode' => $zoneCode, 'memberCode' => $memberCode, 'added' => true]);
    }
}

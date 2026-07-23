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

namespace Sylius\AdminMcpServerPlugin\Tool\Zone;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_zone',
    description: 'create_zone(code, name, type, scope?, memberCodes) → JSON of the newly created Sylius zone. type: "country" | "zone" | "province". scope: "shipping" | "tax" | "all" (default "all"). memberCodes: array of country/zone/province codes — REQUIRED by Sylius (at least 1 member). Example: memberCodes=["US"] for a country zone. IMPORTANT: All country codes in memberCodes must already exist in the system — check list_countries first and use create_country to add any missing ones.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $code        Unique zone code (e.g. "EU", "US").
     * @param string   $name        Zone display name.
     * @param string   $type        Zone type: "country", "zone", "province".
     * @param string   $scope       Zone scope: "shipping", "tax", "all". Default = "all".
     * @param string[] $memberCodes Member codes (country ISO codes, zone codes, or province codes).
     */
    public function __invoke(
        string $code,
        string $name,
        string $type,
        string $scope = 'all',
        array $memberCodes = [],
    ): string {
        if ($memberCodes === []) {
            return (string) json_encode(['error' => 'memberCodes is required — Sylius requires at least 1 zone member. Pass e.g. memberCodes=["US"] for a country zone.']);
        }

        $body = [
            'code' => $code,
            'name' => $name,
            'type' => $type,
            'scope' => $scope,
            'members' => array_map(
                static fn (string $memberCode) => ['code' => $memberCode],
                $memberCodes,
            ),
        ];

        return $this->client->post('zones', $body);
    }
}

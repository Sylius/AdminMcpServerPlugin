<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Zone;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_zone',
    description: <<<'DESC'
update_zone — Updates a zone. Only provided fields are changed; omitted fields keep their current values.

REQUIRED: code (the zone code to update).
OPTIONAL: name, type (country/zone/province), scope (shipping/tax/all), memberCodes (replaces all existing members — provide the full new list).

NOTE: memberCodes replaces the entire member list. Use add_zone_member / remove_zone_member to add or remove individual members without affecting others.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string[] $memberCodes New list of member codes (replaces existing).
     */
    public function __invoke(
        string $code,
        string $name = '',
        string $type = '',
        string $scope = '',
        array $memberCodes = [],
    ): string {
        $existing = json_decode($this->client->get(sprintf('zones/%s', $code)), true);

        $body = [
            'name'  => $name !== '' ? $name : ($existing['name'] ?? $code),
            'type'  => $type !== '' ? $type : ($existing['type'] ?? 'country'),
            'scope' => $scope !== '' ? $scope : ($existing['scope'] ?? 'all'),
        ];

        if ($memberCodes !== []) {
            $body['members'] = array_map(
                static fn (string $memberCode) => ['code' => $memberCode],
                $memberCodes,
            );
        } else {
            $body['members'] = $existing['members'] ?? [];
        }

        return $this->client->put(sprintf('zones/%s', $code), $body);
    }
}

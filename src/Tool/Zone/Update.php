<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Zone;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_zone',
    description: 'update_zone(code, name, type, scope?, memberCodes?) → JSON of the updated Sylius zone. Uses PUT — replaces the full zone. memberCodes replaces all existing members.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $code        Zone code to update.
     * @param string   $name        New display name.
     * @param string   $type        Zone type: "country", "zone", "province".
     * @param string   $scope       Zone scope: "shipping", "tax", "all". Default = "all".
     * @param string[] $memberCodes New list of member codes (replaces existing).
     */
    public function __invoke(
        string $code,
        string $name,
        string $type,
        string $scope = 'all',
        array $memberCodes = [],
    ): string {
        $body = [
            'name' => $name,
            'type' => $type,
            'scope' => $scope,
            'members' => array_map(
                static fn (string $memberCode) => ['code' => $memberCode],
                $memberCodes,
            ),
        ];

        return $this->client->put(sprintf('zones/%s', $code), $body);
    }
}

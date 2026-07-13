<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Zone;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_zone',
    description: 'create_zone(code, name, type, scope?, memberCodes?) → JSON of the newly created Sylius zone. type: "country" | "zone" | "province". scope: "shipping" | "tax" | "all" (default "all"). memberCodes: array of country/zone/province codes to add as members.',
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
        $body = [
            'code' => $code,
            'name' => $name,
            'type' => $type,
            'scope' => $scope,
        ];

        if ($memberCodes !== []) {
            $body['members'] = array_map(
                static fn (string $memberCode) => ['code' => $memberCode],
                $memberCodes,
            );
        }

        return $this->client->post('zones', $body);
    }
}

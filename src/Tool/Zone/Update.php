<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Zone;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_zone',
    description: <<<'DESC'
update_zone(code, body) → JSON of the updated zone. Only fields in body are changed.

body (JSON string) — fields: name (string), type ("country"/"zone"/"province"), scope ("shipping"/"tax"/"all"), memberCodes (array of codes — replaces the entire member list).
NOTE: memberCodes replaces all existing members. Use add_zone_member / remove_zone_member to add or remove individual members without affecting others.
Example: '{"name":"European Union","memberCodes":["DE","FR","PL"]}'
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code, string $body): string
    {
        $existing = json_decode($this->client->get(sprintf('zones/%s', $code)), true);
        $b = json_decode($body, true) ?? [];

        $merged = [
            'name'  => $b['name']  ?? ($existing['name'] ?? $code),
            'type'  => $b['type']  ?? ($existing['type'] ?? 'country'),
            'scope' => $b['scope'] ?? ($existing['scope'] ?? 'all'),
        ];

        if (isset($b['memberCodes']) && $b['memberCodes'] !== []) {
            $merged['members'] = array_map(
                static fn (string $memberCode) => ['code' => $memberCode],
                $b['memberCodes'],
            );
        } else {
            $merged['members'] = $existing['members'] ?? [];
        }

        return $this->client->put(sprintf('zones/%s', $code), $merged);
    }
}

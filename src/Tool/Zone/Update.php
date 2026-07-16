<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Zone;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_zone',
    description: <<<'DESC'
update_zone(code, body) → JSON of the updated zone.

IMPORTANT: First call get_zone to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body.

Convenience: instead of members (array of member objects), you may pass memberCodes (array of code strings) and the tool converts them automatically.
NOTE: memberCodes replaces all existing members. Use add_zone_member / remove_zone_member to add or remove individual members without affecting others.
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
        $b = json_decode($body, true) ?? [];
        if (isset($b['memberCodes'])) {
            $b['members'] = array_map(static fn (string $c) => ['code' => $c], $b['memberCodes']);
            unset($b['memberCodes']);
        }
        return $this->client->put(sprintf('zones/%s', $code), $b);
    }
}

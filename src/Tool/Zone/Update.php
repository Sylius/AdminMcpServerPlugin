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
        if (!\is_array($b)) {
            throw new \TypeError('Zone update body must decode to a JSON object.');
        }

        if (isset($b['memberCodes'])) {
            $memberCodes = $b['memberCodes'];
            if (!\is_array($memberCodes)) {
                throw new \TypeError('memberCodes must be an array of zone member codes.');
            }

            $members = [];
            foreach ($memberCodes as $memberCode) {
                if (!\is_scalar($memberCode)) {
                    throw new \TypeError('Every memberCodes entry must be a scalar zone member code.');
                }

                $members[] = ['code' => (string) $memberCode];
            }

            $b['members'] = $members;
            unset($b['memberCodes']);
        }

        return $this->client->put(sprintf('zones/%s', $code), $b);
    }
}

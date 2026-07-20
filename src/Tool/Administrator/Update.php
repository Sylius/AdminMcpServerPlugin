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

namespace Sylius\AdminMcpServerPlugin\Tool\Administrator;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_administrator',
    description: <<<'DESC'
update_administrator(id, body) → JSON object of the updated Sylius administrator.

body is a JSON string containing only the fields you want to change:
- email (string) — new email address
- username (string) — new username
- firstName (string) — new first name
- lastName (string) — new last name
- localeCode (string) — new UI locale, e.g. "en_US"
- enabled (bool) — whether the account is active
- plainPassword (string) — new password in plain text

Example: '{"firstName":"Jan","lastName":"Kowalski","enabled":true}'
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(int $id, string $body): string
    {
        return $this->client->put(sprintf('administrators/%d', $id), json_decode($body, true) ?? []);
    }
}

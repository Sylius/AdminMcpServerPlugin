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

namespace Sylius\AdminMcpServerPlugin\Tool\Country;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_country',
    description: 'update_country(code, body) → JSON of the updated country. body (JSON string) — fields: enabled (bool). Example: \'{"enabled": true}\'',
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(string $code, string $body): string
    {
        return $this->client->put(sprintf('countries/%s', $code), json_decode($body, true) ?? []);
    }
}

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

namespace Sylius\AdminMcpServerPlugin\Tool\CustomerGroup;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_customer_group',
    description: 'update_customer_group(code, body) → JSON of the updated customer group. body (JSON string) — fields: name (string). Example: \'{"name": "VIP Customers"}\'',
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(string $code, string $body): string
    {
        return $this->client->put(sprintf('customer-groups/%s', $code), json_decode($body, true) ?? []);
    }
}

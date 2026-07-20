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
    name: 'create_customer_group',
    description: 'create_customer_group(code, name) → JSON object of the newly created Sylius customer group. code must be unique.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Unique customer group code (e.g. "vip").
     * @param string $name Customer group display name (e.g. "VIP Customers").
     */
    public function __invoke(string $code, string $name): string
    {
        return $this->client->post('customer-groups', [
            'code' => $code,
            'name' => $name,
        ]);
    }
}

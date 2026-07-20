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
    name: 'delete_customer_group',
    description: 'delete_customer_group(code) → empty string on success (HTTP 204). Permanently deletes the Sylius customer group with the given code.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Customer group code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('customer-groups/%s', $code));
    }
}

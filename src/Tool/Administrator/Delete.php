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
    name: 'delete_administrator',
    description: 'delete_administrator(id) → empty string on success (HTTP 204). Permanently deletes the Sylius administrator with the given ID. Cannot delete your own account.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Administrator ID to delete.
     */
    public function __invoke(int $id): string
    {
        return $this->client->delete(sprintf('administrators/%d', $id));
    }
}

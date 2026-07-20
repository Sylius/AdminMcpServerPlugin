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
    name: 'get_administrator',
    description: 'get_administrator(id) → JSON object of a single Sylius administrator. Returns: id, username, email, firstName, lastName, localeCode, enabled, lastLogin, createdAt, updatedAt.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Administrator ID.
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('administrators/%d', $id));
    }
}

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

namespace Sylius\AdminMcpServerPlugin\Tool\Promotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'delete_promotion',
    description: 'delete_promotion(code) → Permanently deletes a Sylius cart promotion and all its coupons. Returns empty string on success (HTTP 204). Use archive_promotion to soft-delete instead.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Promotion code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('promotions/%s', $code));
    }
}

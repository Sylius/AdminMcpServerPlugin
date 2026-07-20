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

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingCategory;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'delete_shipping_category',
    description: 'delete_shipping_category(code) → Permanently deletes a shipping category. Returns empty response on success (204). NOTE: Do not delete a category that is still assigned to shipping methods — unassign it first.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('shipping-categories/%s', $code));
    }
}

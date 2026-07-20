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

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_order_items',
    description: 'list_order_items(tokenValue) → JSON array of items for a Sylius order. Each item has: id, quantity, unitPrice, total (both in smallest currency unit, e.g. 1150 = €11.50), productName, variantName, units.',
)]
final readonly class ListItems
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $tokenValue Order token value.
     */
    public function __invoke(string $tokenValue): string
    {
        $order = json_decode($this->client->get(sprintf('orders/%s', $tokenValue)), true);

        return (string) json_encode($order['items'] ?? []);
    }
}

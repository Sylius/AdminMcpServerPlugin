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
    name: 'get_order_item',
    description: 'get_order_item(id) → Gets details of a single order line item by its numeric ID. Returns: id, productName, variantName, variant, quantity, unitPrice (in smallest currency unit, e.g. 1000 = 10.00), total, units, adjustments. Get the item ID from list_order_items(tokenValue) or from the items array in get_order(tokenValue).',
)]
final readonly class GetItem
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('order-items/%d', $id));
    }
}

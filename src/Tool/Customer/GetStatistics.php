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

namespace Sylius\AdminMcpServerPlugin\Tool\Customer;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_customer_statistics',
    description: 'get_customer_statistics(id) → Purchase statistics for a customer. Returns: allOrdersCount (total number of orders placed), perChannelsStatistics (breakdown per sales channel showing order count and total spent amount per channel). Useful to identify VIP customers or to check purchase history at a glance.',
)]
final readonly class GetStatistics
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Customer ID.
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('customers/%d/statistics', $id));
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Customer;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

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

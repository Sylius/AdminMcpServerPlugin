<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Customer;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_customer_statistics',
    description: 'get_customer_statistics(id) → JSON object with customer order statistics. Returns: allOrdersCount (total orders across all channels), perChannelsStatistics (collection with per-channel order count and totals).',
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

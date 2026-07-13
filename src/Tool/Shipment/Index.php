<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Shipment;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_shipments',
    description: 'list_shipments(page?, itemsPerPage?, state?, orderTokenValue?) → JSON Hydra collection of all shipments. Each shipment has: id, state (ready/shipped/cancelled), method, order, tracking, shippedAt, createdAt. Filter by state (e.g. "ready") or orderTokenValue.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int    $page           Page number. Default = 1.
     * @param int    $itemsPerPage   Items per page. Default = 30.
     * @param string $state          Filter by shipment state (e.g. "ready", "shipped", "cancelled"). Default = "".
     * @param string $orderTokenValue Filter by order token. Default = "".
     */
    public function __invoke(
        int $page = 1,
        int $itemsPerPage = 30,
        string $state = '',
        string $orderTokenValue = '',
    ): string {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];

        if ($state !== '') {
            $params['state'] = $state;
        }
        if ($orderTokenValue !== '') {
            $params['order.tokenValue'] = $orderTokenValue;
        }

        return $this->client->get('shipments', $params);
    }
}

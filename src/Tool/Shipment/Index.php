<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Shipment;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_shipments',
    description: 'list_shipments(page?, itemsPerPage?, state?, methodCode?) → Lists all shipments across all orders. Each shipment has: id, state (ready=waiting to be shipped / shipped=already sent / cancelled), method (shipping carrier), order (IRI — last segment is the tokenValue), tracking (tracking number if set), shippedAt. Filter by state="ready" to find shipments waiting to be dispatched. Filter by methodCode (e.g. "dhl_eea") for a specific carrier. Use ship_shipment(id) to mark one as shipped. To see shipments for one specific order, use list_order_shipments(tokenValue) instead.',
)]
final readonly class Index
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(
        int $page = 1,
        int $itemsPerPage = 30,
        string $state = '',
        string $methodCode = '',
    ): string {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        if ($state !== '') { $params['state'] = $state; }
        if ($methodCode !== '') { $params['method.code'] = $methodCode; }
        return $this->client->get('shipments', $params);
    }
}

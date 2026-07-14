<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Payment;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_payments',
    description: 'list_payments(page?, itemsPerPage?, state?) → Lists all payments across all orders. Each payment has: id, state (new=not yet paid / completed=paid / refunded / failed / cancelled), method (payment gateway), amount, currencyCode, order (IRI — last segment is the tokenValue). Filter by state to find payments needing action: state="new" for unpaid, state="completed" for paid. Use complete_payment(id) or refund_payment(id) to change state. To list payments for one specific order, use list_order_payments(tokenValue) instead.',
)]
final readonly class Index
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(
        int $page = 1,
        int $itemsPerPage = 30,
        string $state = '',
    ): string {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        if ($state !== '') { $params['state'] = $state; }
        return $this->client->get('payments', $params);
    }
}

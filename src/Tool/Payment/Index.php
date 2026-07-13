<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Payment;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_payments',
    description: 'list_payments(page?, itemsPerPage?, state?, orderTokenValue?) → JSON Hydra collection of all payments. Each payment has: id, state (new/processing/completed/failed/cancelled/refunded), method, amount, currencyCode, order. Filter by state or orderTokenValue.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int    $page            Page number. Default = 1.
     * @param int    $itemsPerPage    Items per page. Default = 30.
     * @param string $state           Filter by payment state (e.g. "new", "completed", "refunded"). Default = "".
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

        return $this->client->get('payments', $params);
    }
}

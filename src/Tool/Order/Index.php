<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_orders',
    description: 'list_orders(page?, itemsPerPage?, state?, number?, customerEmail?, channelCode?) → JSON Hydra collection of Sylius orders. Each order has: tokenValue (use this as identifier), number, state (new/cart/addressed/payment_selected/shipping_selected/partially_shipped/shipped/cancelled/fulfilled), paymentState (awaiting_payment/paid/partially_refunded/refunded), shippingState, customer, channel, total, items, shipments, payments, createdAt.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int    $page          Page number (1-based). Default = 1.
     * @param int    $itemsPerPage  Items per page. Default = 30.
     * @param string $state         Filter by order state (e.g. "new", "fulfilled", "cancelled"). Default = "".
     * @param string $number        Filter by order number (e.g. "#000000001"). Default = "".
     * @param string $customerEmail Filter by customer email. Default = "".
     * @param string $channelCode   Filter by channel code. Default = "".
     */
    public function __invoke(
        int $page = 1,
        int $itemsPerPage = 30,
        string $state = '',
        string $number = '',
        string $customerEmail = '',
        string $channelCode = '',
    ): string {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];

        if ($state !== '') {
            $params['state'] = $state;
        }
        if ($number !== '') {
            $params['number'] = $number;
        }
        if ($customerEmail !== '') {
            $params['customer.email'] = $customerEmail;
        }
        if ($channelCode !== '') {
            $params['channel.code'] = $channelCode;
        }

        return $this->client->get('orders', $params);
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Coupon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_coupon',
    description: 'get_coupon(promotionCode, couponCode) → JSON of a single coupon. Returns: id, code, usageLimit, perCustomerUsageLimit, used, reusableFromCancelledOrders, expiresAt.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $promotionCode, string $couponCode): string
    {
        return $this->client->get(sprintf('promotions/%s/coupons/%s', $promotionCode, $couponCode));
    }
}

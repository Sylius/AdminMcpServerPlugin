<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Coupon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_coupon',
    description: 'update_coupon(promotionCode, couponCode, usageLimit?, perCustomerUsageLimit?, expiresAt?, reusableFromCancelledOrders?) → JSON of the updated coupon. Uses PUT.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $promotionCode              Promotion code the coupon belongs to.
     * @param string   $couponCode                 Coupon code to update.
     * @param int|null $usageLimit                 New total usage limit. Null = unlimited.
     * @param int|null $perCustomerUsageLimit      New per-customer limit. Null = unlimited.
     * @param string   $expiresAt                  New expiry datetime ISO 8601. Default = "" (no expiry).
     * @param bool     $reusableFromCancelledOrders Reusability after order cancellation. Default = false.
     */
    public function __invoke(
        string $promotionCode,
        string $couponCode,
        ?int $usageLimit = null,
        ?int $perCustomerUsageLimit = null,
        string $expiresAt = '',
        bool $reusableFromCancelledOrders = false,
    ): string {
        $body = [
            'reusableFromCancelledOrders' => $reusableFromCancelledOrders,
            'usageLimit' => $usageLimit,
            'perCustomerUsageLimit' => $perCustomerUsageLimit,
            'expiresAt' => $expiresAt !== '' ? $expiresAt : null,
        ];

        return $this->client->put(
            sprintf('promotions/%s/coupons/%s', $promotionCode, $couponCode),
            $body,
        );
    }
}

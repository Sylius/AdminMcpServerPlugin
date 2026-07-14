<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Coupon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_coupon',
    description: 'update_coupon(promotionCode, couponCode, usageLimit?, perCustomerUsageLimit?, expiresAt?, reusableFromCancelledOrders?) → JSON of the updated coupon. Only provided fields are changed; omitted fields keep their current values.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(
        string $promotionCode,
        string $couponCode,
        ?int $usageLimit = null,
        ?int $perCustomerUsageLimit = null,
        string $expiresAt = '',
        ?bool $reusableFromCancelledOrders = null,
    ): string {
        $existing = json_decode(
            $this->client->get(sprintf('promotions/%s/coupons/%s', $promotionCode, $couponCode)),
            true,
        );

        $body = [
            'reusableFromCancelledOrders' => $reusableFromCancelledOrders ?? ($existing['reusableFromCancelledOrders'] ?? false),
            'usageLimit'            => $usageLimit ?? ($existing['usageLimit'] ?? null),
            'perCustomerUsageLimit' => $perCustomerUsageLimit ?? ($existing['perCustomerUsageLimit'] ?? null),
            'expiresAt'             => $expiresAt !== '' ? $expiresAt : ($existing['expiresAt'] ?? null),
        ];

        return $this->client->put(
            sprintf('promotions/%s/coupons/%s', $promotionCode, $couponCode),
            $body,
        );
    }
}

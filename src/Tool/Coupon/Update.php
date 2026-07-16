<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Coupon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_coupon',
    description: <<<'DESC'
update_coupon(promotionCode, couponCode, body) → JSON of the updated coupon. Only fields in body are changed.

body (JSON string) — fields: usageLimit (int), perCustomerUsageLimit (int), expiresAt ("YYYY-MM-DDTHH:MM:SS"), reusableFromCancelledOrders (bool).
Example: '{"usageLimit":100,"expiresAt":"2024-12-31T23:59:59"}'
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $promotionCode, string $couponCode, string $body): string
    {
        $existing = json_decode(
            $this->client->get(sprintf('promotions/%s/coupons/%s', $promotionCode, $couponCode)),
            true,
        );
        $b = json_decode($body, true) ?? [];

        return $this->client->put(
            sprintf('promotions/%s/coupons/%s', $promotionCode, $couponCode),
            [
                'reusableFromCancelledOrders' => $b['reusableFromCancelledOrders'] ?? ($existing['reusableFromCancelledOrders'] ?? false),
                'usageLimit'            => $b['usageLimit']            ?? ($existing['usageLimit'] ?? null),
                'perCustomerUsageLimit' => $b['perCustomerUsageLimit'] ?? ($existing['perCustomerUsageLimit'] ?? null),
                'expiresAt'             => $b['expiresAt']             ?? ($existing['expiresAt'] ?? null),
            ],
        );
    }
}

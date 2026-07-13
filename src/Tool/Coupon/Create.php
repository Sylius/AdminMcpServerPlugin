<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Coupon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_coupon',
    description: 'create_coupon(promotionCode, code, usageLimit?, perCustomerUsageLimit?, expiresAt?, reusableFromCancelledOrders?) → JSON of the newly created coupon. The promotion must have couponBased=true.',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $promotionCode              Promotion code to attach the coupon to (must be couponBased).
     * @param string   $code                       Unique coupon code customers will enter (e.g. "SUMMER20").
     * @param int|null $usageLimit                 Total number of times this coupon can be used. Null = unlimited.
     * @param int|null $perCustomerUsageLimit      Max uses per individual customer. Null = unlimited.
     * @param string   $expiresAt                  Expiry datetime in ISO 8601. Default = "" (no expiry).
     * @param bool     $reusableFromCancelledOrders Whether the coupon can be reused after order cancellation. Default = false.
     */
    public function __invoke(
        string $promotionCode,
        string $code,
        ?int $usageLimit = null,
        ?int $perCustomerUsageLimit = null,
        string $expiresAt = '',
        bool $reusableFromCancelledOrders = false,
    ): string {
        $body = [
            'code' => $code,
            'reusableFromCancelledOrders' => $reusableFromCancelledOrders,
        ];

        if ($usageLimit !== null) {
            $body['usageLimit'] = $usageLimit;
        }
        if ($perCustomerUsageLimit !== null) {
            $body['perCustomerUsageLimit'] = $perCustomerUsageLimit;
        }
        if ($expiresAt !== '') {
            $body['expiresAt'] = $expiresAt;
        }

        return $this->client->post(sprintf('promotions/%s/coupons', $promotionCode), $body);
    }
}

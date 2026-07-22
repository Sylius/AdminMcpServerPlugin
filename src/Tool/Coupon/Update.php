<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Coupon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_coupon',
    description: <<<'DESC'
update_coupon(promotionCode, couponCode, body) → JSON of the updated coupon.

You can pass a partial body with only the fields you want to change — e.g. {"usageLimit":100} works without fetching the full JSON first. Available fields: usageLimit, perCustomerUsageLimit, reusableFromCancelledOrders, expiresAt.
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
        return $this->client->put(
            sprintf('promotions/%s/coupons/%s', $promotionCode, $couponCode),
            json_decode($body, true) ?? [],
        );
    }
}

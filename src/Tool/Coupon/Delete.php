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
    name: 'delete_coupon',
    description: 'delete_coupon(promotionCode, couponCode) → Permanently deletes a coupon from a Sylius promotion. Returns empty string on success (HTTP 204).',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $promotionCode Promotion code the coupon belongs to.
     * @param string $couponCode    Coupon code to delete.
     */
    public function __invoke(string $promotionCode, string $couponCode): string
    {
        return $this->client->delete(
            sprintf('promotions/%s/coupons/%s', $promotionCode, $couponCode),
        );
    }
}

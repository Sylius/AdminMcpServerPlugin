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

IMPORTANT: First call get_coupon(promotionCode, couponCode) to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body.
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

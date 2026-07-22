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
    name: 'list_coupons',
    description: 'list_coupons(promotionCode, page?, itemsPerPage?) → JSON Hydra collection of coupons for a given Sylius promotion. Each coupon has: id, code, usageLimit, perCustomerUsageLimit, used, reusableFromCancelledOrders, expiresAt. Note: if promotionCode does not exist, an empty collection is returned (not a 404 error) — verify the code with get_promotion first if in doubt.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $promotionCode Promotion code whose coupons to list.
     * @param int    $page          Page number (1-based). Default = 1.
     * @param int    $itemsPerPage  Items per page. Default = 30.
     */
    public function __invoke(string $promotionCode, int $page = 1, int $itemsPerPage = 30): string
    {
        return $this->client->get(sprintf('promotions/%s/coupons', $promotionCode), [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

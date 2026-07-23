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
    name: 'create_coupon',
    description: 'create_coupon(promotionCode, code, usageLimit?, perCustomerUsageLimit?, expiresAt?, reusableFromCancelledOrders?) → JSON of the newly created coupon. Automatically enables couponBased on the promotion if it is not already set.',
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
        /** @var array<string, mixed> $promotion */
        $promotion = json_decode($this->client->get(sprintf('promotions/%s', $promotionCode)), true);
        if (!($promotion['couponBased'] ?? false)) {
            /** @var array<int, array<string, mixed>> $rawRules */
            $rawRules = \is_array($promotion['rules'] ?? null) ? $promotion['rules'] : [];
            $rules = array_map(
                fn (array $item): array => array_diff_key($item, ['@id' => null, '@type' => null, 'id' => null]),
                $rawRules,
            );
            /** @var array<int, array<string, mixed>> $rawActions */
            $rawActions = \is_array($promotion['actions'] ?? null) ? $promotion['actions'] : [];
            $actions = array_map(
                fn (array $item): array => array_diff_key($item, ['@id' => null, '@type' => null, 'id' => null]),
                $rawActions,
            );
            $putBody = [
                'code' => $promotion['code'],
                'name' => $promotion['name'],
                'priority' => $promotion['priority'] ?? 0,
                'exclusive' => $promotion['exclusive'] ?? false,
                'couponBased' => true,
                'channels' => $promotion['channels'] ?? [],
                'rules' => $rules,
                'actions' => $actions,
            ];
            if (isset($promotion['description'])) {
                $putBody['description'] = $promotion['description'];
            }
            if (isset($promotion['usageLimit'])) {
                $putBody['usageLimit'] = $promotion['usageLimit'];
            }
            if (isset($promotion['startsAt'])) {
                $putBody['startsAt'] = $promotion['startsAt'];
            }
            if (isset($promotion['endsAt'])) {
                $putBody['endsAt'] = $promotion['endsAt'];
            }
            $this->client->put(sprintf('promotions/%s', $promotionCode), $putBody);
        }

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

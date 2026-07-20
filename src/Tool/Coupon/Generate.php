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
    name: 'generate_coupons',
    description: 'generate_coupons(promotionCode, amount, codeLength, prefix?, suffix?, usageLimit?, expiresAt?) → Generates multiple random coupon codes for a Sylius promotion in bulk. Returns JSON with the generation result. The promotion must have couponBased=true.',
)]
final readonly class Generate
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $promotionCode Promotion code to generate coupons for (must be couponBased).
     * @param int      $amount        Number of coupons to generate.
     * @param int      $codeLength    Length of each generated coupon code (excluding prefix/suffix).
     * @param string   $prefix        Optional prefix for generated codes (e.g. "SUMMER_"). Default = "".
     * @param string   $suffix        Optional suffix for generated codes (e.g. "_2025"). Default = "".
     * @param int|null $usageLimit    Usage limit per coupon. Null = unlimited.
     * @param string   $expiresAt     Expiry datetime ISO 8601. Default = "" (no expiry).
     */
    public function __invoke(
        string $promotionCode,
        int $amount,
        int $codeLength,
        string $prefix = '',
        string $suffix = '',
        ?int $usageLimit = null,
        string $expiresAt = '',
    ): string {
        $body = [
            'amount' => $amount,
            'codeLength' => $codeLength,
        ];

        if ($prefix !== '') {
            $body['prefix'] = $prefix;
        }
        if ($suffix !== '') {
            $body['suffix'] = $suffix;
        }
        if ($usageLimit !== null) {
            $body['usageLimit'] = $usageLimit;
        }
        if ($expiresAt !== '') {
            $body['expiresAt'] = $expiresAt;
        }

        return $this->client->post(
            sprintf('promotions/%s/coupons/generate', $promotionCode),
            $body,
        );
    }
}

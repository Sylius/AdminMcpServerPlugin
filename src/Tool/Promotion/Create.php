<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Promotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_promotion',
    description: 'create_promotion(code, name, channelCodes, description?, priority?, exclusive?, usageLimit?, couponBased?, startsAt?, endsAt?, rules?, actions?) → JSON of the newly created Sylius cart promotion. CONFIGURATION FORMATS — percentage actions (order_percentage_discount, unit_percentage_discount, shipping_percentage_discount): {"percentage":0.1}. Fixed/amount rules and actions (item_total rule, order_fixed_discount, unit_fixed_discount): {"CHANNEL_CODE":{"amount":5000}} — must list ALL channel codes in the system. EXAMPLES — 10% off entire order: actions=[{"type":"order_percentage_discount","configuration":{"percentage":0.1}}]. Minimum order 50 EUR rule: rules=[{"type":"item_total","configuration":{"FASHION_WEB":{"amount":5000},"WEB_EUR":{"amount":5000}}}]. Other rule types: cart_quantity (config:{"count":N}), has_taxon (config:{"taxons":["CODE"]}), nth_order (config:{"nth":N}), customer_group (config:{"group_code":"CODE"}).',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $code         Unique promotion code.
     * @param string   $name         Promotion display name.
     * @param string[] $channelCodes Channel codes where this promotion is active.
     * @param string   $description  Optional description. Default = "".
     * @param int      $priority     Application priority (higher = applied first). Default = 0.
     * @param bool     $exclusive    If true, no other promotions apply alongside this one. Default = false.
     * @param int|null $usageLimit   Total usage limit across all customers. Null = unlimited.
     * @param bool     $couponBased  If true, customers must enter a coupon code. Default = false.
     * @param string   $startsAt     Start datetime in ISO 8601 (e.g. "2025-01-01T00:00:00+00:00"). Default = "".
     * @param string   $endsAt       End datetime in ISO 8601. Default = "".
     * @param array    $rules        Array of rule objects with "type" and "configuration" keys.
     * @param array    $actions      Array of action objects with "type" and "configuration" keys.
     */
    public function __invoke(
        string $code,
        string $name,
        array $channelCodes,
        string $description = '',
        int $priority = 0,
        bool $exclusive = false,
        ?int $usageLimit = null,
        bool $couponBased = false,
        string $startsAt = '',
        string $endsAt = '',
        array $rules = [],
        array $actions = [],
    ): string {
        $body = [
            'code' => $code,
            'name' => $name,
            'priority' => $priority,
            'exclusive' => $exclusive,
            'couponBased' => $couponBased,
            'channels' => array_map(
                static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c),
                $channelCodes,
            ),
            'rules' => $rules,
            'actions' => $actions,
        ];

        if ($description !== '') {
            $body['description'] = $description;
        }
        if ($usageLimit !== null) {
            $body['usageLimit'] = $usageLimit;
        }
        if ($startsAt !== '') {
            $body['startsAt'] = $startsAt;
        }
        if ($endsAt !== '') {
            $body['endsAt'] = $endsAt;
        }

        return $this->client->post('promotions', $body);
    }
}

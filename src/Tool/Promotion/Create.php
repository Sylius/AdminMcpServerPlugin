<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Promotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_promotion',
    description: <<<'DESC'
create_promotion — Creates a cart promotion (discount applied at checkout when conditions are met). Prerequisites: run list_channels to get channelCodes.

REQUIRED: code (unique ID, e.g. "SUMMER10"), name (e.g. "10% Summer Discount"), channelCodes.

rules (JSON string) — WHEN to apply the discount (optional, leave '[]' for always):
- Minimum order total: '[{"type":"item_total","configuration":{"CHANNEL_CODE":{"amount":5000}}}]' (5000 = 50.00; must include ALL channel codes)
- Minimum quantity: '[{"type":"cart_quantity","configuration":{"count":3}}]'
- Customer in group: '[{"type":"customer_group","configuration":{"group_code":"GROUP_CODE"}}]'
- Products from taxon: '[{"type":"has_taxon","configuration":{"taxons":["TAXON_CODE"]}}]'
- Nth order for customer: '[{"type":"nth_order","configuration":{"nth":5}}]'

actions (JSON string) — WHAT discount to give (required):
- % off whole order: '[{"type":"order_percentage_discount","configuration":{"percentage":0.1}}]' (0.1 = 10%)
- % off each item: '[{"type":"unit_percentage_discount","configuration":{"percentage":0.15}}]'
- % off shipping: '[{"type":"shipping_percentage_discount","configuration":{"percentage":1.0}}]' (free shipping)
- Fixed amount off order: '[{"type":"order_fixed_discount","configuration":{"CHANNEL_CODE":{"amount":1000}}}]'
- Fixed amount off each item: '[{"type":"unit_fixed_discount","configuration":{"CHANNEL_CODE":{"amount":200}}}]'

NOTE for amount-based rules/actions: configuration must include ALL channel codes in the system. Use list_channels to get them all.
Ask user: what is the discount (% or amount)? Any minimum order condition? Which channels?
DESC,
)]
final readonly class Create
{
    public function __construct(private ApiClientInterface $client) {}

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
        string $rules = '[]',
        string $actions = '[]',
    ): string {
        $body = [
            'code'        => $code,
            'name'        => $name,
            'priority'    => $priority,
            'exclusive'   => $exclusive,
            'couponBased' => $couponBased,
            'channels'    => array_map(
                static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c),
                $channelCodes,
            ),
            'rules'   => json_decode($rules, true) ?? [],
            'actions' => json_decode($actions, true) ?? [],
        ];

        if ($description !== '') { $body['description'] = $description; }
        if ($usageLimit !== null) { $body['usageLimit'] = $usageLimit; }
        if ($startsAt !== '') { $body['startsAt'] = $startsAt; }
        if ($endsAt !== '') { $body['endsAt'] = $endsAt; }

        return $this->client->post('promotions', $body);
    }
}

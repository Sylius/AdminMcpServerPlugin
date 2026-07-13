<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Promotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_promotion',
    description: 'update_promotion(code, name, channelCodes, description?, priority?, exclusive?, usageLimit?, couponBased?, startsAt?, endsAt?, rules?, actions?) → JSON of the updated Sylius cart promotion. Uses PUT — provide all fields you want to keep. Omitting rules/actions preserves existing ones. CONFIGURATION FORMATS — percentage actions (order_percentage_discount, unit_percentage_discount, shipping_percentage_discount): {"percentage":0.1}. Fixed/amount (item_total rule, order_fixed_discount, unit_fixed_discount): {"CHANNEL_CODE":{"amount":N}} for ALL channel codes.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string   $code         Promotion code to update.
     * @param string   $name         New display name.
     * @param string[] $channelCodes Channel codes (replaces existing list).
     * @param string   $description  Description. Default = "".
     * @param int      $priority     Application priority. Default = 0.
     * @param bool     $exclusive    Exclusive flag. Default = false.
     * @param int|null $usageLimit   Usage limit. Null = unlimited.
     * @param bool     $couponBased  Coupon-based flag. Default = false.
     * @param string   $startsAt     Start datetime ISO 8601. Default = "".
     * @param string   $endsAt       End datetime ISO 8601. Default = "".
     * @param array    $rules        Rules array. Empty = preserve existing rules.
     * @param array    $actions      Actions array. Empty = preserve existing actions.
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
        $existing = json_decode($this->client->get(sprintf('promotions/%s', $code)), true);

        $body = [
            'name' => $name,
            'priority' => $priority,
            'exclusive' => $exclusive,
            'couponBased' => $couponBased,
            'channels' => array_map(
                static fn (string $c) => sprintf('/api/v2/admin/channels/%s', $c),
                $channelCodes,
            ),
            'rules' => $rules !== [] ? $rules : ($existing['rules'] ?? []),
            'actions' => $actions !== [] ? $actions : ($existing['actions'] ?? []),
        ];

        if ($description !== '') {
            $body['description'] = $description;
        }
        $body['usageLimit'] = $usageLimit;
        $body['startsAt'] = $startsAt !== '' ? $startsAt : null;
        $body['endsAt'] = $endsAt !== '' ? $endsAt : null;

        return $this->client->put(sprintf('promotions/%s', $code), $body);
    }
}

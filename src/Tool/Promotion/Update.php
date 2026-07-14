<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Promotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_promotion',
    description: <<<'DESC'
update_promotion — Updates a cart promotion. Only provided fields are changed; omitted fields keep their current values.

REQUIRED: code (the promotion code to update).
OPTIONAL: name, channelCodes, description, priority, exclusive, usageLimit, couponBased, startsAt, endsAt.
OPTIONAL: rules/actions (JSON strings — omit or pass '[]' to keep existing):
- rules examples: '[{"type":"item_total","configuration":{"CHANNEL_CODE":{"amount":5000}}}]' — min order 50.00; '[{"type":"cart_quantity","configuration":{"count":3}}]' — min 3 items
- actions examples: '[{"type":"order_percentage_discount","configuration":{"percentage":0.1}}]' — 10% off order; '[{"type":"shipping_percentage_discount","configuration":{"percentage":1.0}}]' — free shipping; '[{"type":"order_fixed_discount","configuration":{"CHANNEL_CODE":{"amount":1000}}}]' — fixed 10.00 off (ALL channels required)

To replace rules/actions with nothing pass '[{"type":"..."}]' with any value to trigger replacement.
DESC,
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client) {}

    /**
     * @param string[] $channelCodes
     */
    public function __invoke(
        string $code,
        string $name = '',
        array $channelCodes = [],
        string $description = '',
        int $priority = -1,
        ?bool $exclusive = null,
        ?int $usageLimit = null,
        ?bool $couponBased = null,
        string $startsAt = '',
        string $endsAt = '',
        string $rules = '[]',
        string $actions = '[]',
    ): string {
        $existing = json_decode($this->client->get(sprintf('promotions/%s', $code)), true);

        $decodedRules   = json_decode($rules, true);
        $decodedActions = json_decode($actions, true);

        $body = [
            'name'        => $name !== '' ? $name : ($existing['name'] ?? $code),
            'priority'    => $priority >= 0 ? $priority : ($existing['priority'] ?? 0),
            'exclusive'   => $exclusive ?? ($existing['exclusive'] ?? false),
            'couponBased' => $couponBased ?? ($existing['couponBased'] ?? false),
            'channels'    => $channelCodes !== []
                ? array_map(fn (string $c) => $this->client->iri(sprintf('channels/%s', $c)), $channelCodes)
                : ($existing['channels'] ?? []),
            'rules'   => ($decodedRules !== null && $decodedRules !== [])   ? $decodedRules   : $this->stripMeta($existing['rules']   ?? []),
            'actions' => ($decodedActions !== null && $decodedActions !== []) ? $decodedActions : $this->stripMeta($existing['actions'] ?? []),
        ];

        if ($description !== '') {
            $body['description'] = $description;
        }

        $body['usageLimit'] = $usageLimit ?? ($existing['usageLimit'] ?? null);
        $body['startsAt']   = $startsAt !== '' ? $startsAt : ($existing['startsAt'] ?? null);
        $body['endsAt']     = $endsAt !== '' ? $endsAt : ($existing['endsAt'] ?? null);

        return $this->client->put(sprintf('promotions/%s', $code), $body);
    }

    /** @param array<int, array<string, mixed>> $items */
    private function stripMeta(array $items): array
    {
        return array_map(static function (array $item): array {
            unset($item['@id'], $item['@type'], $item['id']);
            return $item;
        }, $items);
    }
}

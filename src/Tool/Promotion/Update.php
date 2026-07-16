<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Promotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_promotion',
    description: <<<'DESC'
update_promotion(code, body) → JSON of the updated cart promotion. Only fields in body are changed.

body (JSON string) — fields: name (string), channels (array of channel IRIs from list_channels @id), description (string), priority (int), exclusive (bool), usageLimit (int), couponBased (bool), startsAt ("YYYY-MM-DDTHH:MM:SS"), endsAt ("YYYY-MM-DDTHH:MM:SS"), rules (array), actions (array).
rules examples: [{"type":"item_total","configuration":{"CHANNEL_CODE":{"amount":5000}}}] — min order 50.00; [{"type":"cart_quantity","configuration":{"count":3}}] — min 3 items
actions examples: [{"type":"order_percentage_discount","configuration":{"percentage":0.1}}] — 10% off order; [{"type":"order_fixed_discount","configuration":{"CHANNEL_CODE":{"amount":1000}}}] — fixed 10.00 off (ALL channels required)
Example: '{"name":"Summer Sale","priority":10}'
DESC,
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(string $code, string $body): string
    {
        $existing = json_decode($this->client->get(sprintf('promotions/%s', $code)), true);
        $b = json_decode($body, true) ?? [];

        $merged = [
            'name'        => $b['name']        ?? ($existing['name'] ?? $code),
            'priority'    => $b['priority']    ?? ($existing['priority'] ?? 0),
            'exclusive'   => $b['exclusive']   ?? ($existing['exclusive'] ?? false),
            'couponBased' => $b['couponBased'] ?? ($existing['couponBased'] ?? false),
            'channels'    => $b['channels']    ?? ($existing['channels'] ?? []),
            'rules'       => array_key_exists('rules', $b)   ? $b['rules']   : $this->stripMeta($existing['rules']   ?? []),
            'actions'     => array_key_exists('actions', $b) ? $b['actions'] : $this->stripMeta($existing['actions'] ?? []),
        ];

        foreach (['description', 'usageLimit', 'startsAt', 'endsAt'] as $key) {
            $merged[$key] = $b[$key] ?? ($existing[$key] ?? null);
        }

        return $this->client->put(sprintf('promotions/%s', $code), $merged);
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

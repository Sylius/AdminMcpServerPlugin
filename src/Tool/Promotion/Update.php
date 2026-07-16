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
            'rules'       => array_key_exists('rules', $b)   ? $b['rules']   : $this->stripAndFillChannels($existing['rules']   ?? []),
            'actions'     => array_key_exists('actions', $b) ? $b['actions'] : $this->stripAndFillChannels($existing['actions'] ?? []),
        ];

        foreach (['description', 'usageLimit', 'startsAt', 'endsAt'] as $key) {
            $merged[$key] = $b[$key] ?? ($existing[$key] ?? null);
        }

        return $this->client->put(sprintf('promotions/%s', $code), $merged);
    }

    /**
     * Strips JSON-LD meta (@id, @type, id) from each item, and for items with
     * per-channel configuration (keys that look like channel codes) auto-fills
     * any channels currently in the system that are missing from the stored config.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    private function stripAndFillChannels(array $items): array
    {
        if ($items === []) {
            return [];
        }

        $allChannelCodes = null;

        return array_map(function (array $item) use (&$allChannelCodes): array {
            unset($item['@id'], $item['@type'], $item['id']);

            $config = $item['configuration'] ?? [];
            if (!$this->hasChannelKeys($config)) {
                return $item;
            }

            // Lazy-fetch all channel codes once
            if ($allChannelCodes === null) {
                $channelsData = json_decode($this->client->get('channels', ['pagination' => false]), true);
                $allChannelCodes = array_column($channelsData['hydra:member'] ?? [], 'code');
            }

            // Determine a template from the first existing entry
            $template = $config ? reset($config) : [];
            if (is_array($template)) {
                $template = array_fill_keys(array_keys($template), 0);
            }

            foreach ($allChannelCodes as $channelCode) {
                if (!array_key_exists($channelCode, $config)) {
                    $config[$channelCode] = $template;
                }
            }
            $item['configuration'] = $config;

            return $item;
        }, $items);
    }

    /**
     * Returns true if the config array has uppercase string keys (channel codes).
     * @param array<string, mixed> $config
     */
    private function hasChannelKeys(array $config): bool
    {
        foreach (array_keys($config) as $key) {
            if (is_string($key) && strtoupper($key) === $key && strlen($key) >= 2) {
                return true;
            }
        }
        return false;
    }
}

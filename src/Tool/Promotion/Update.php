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

namespace Sylius\AdminMcpServerPlugin\Tool\Promotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_promotion',
    description: <<<'DESC'
update_promotion(code, body) → JSON of the updated cart promotion.

You can pass a partial body with only the fields you want to change — e.g. {"name":"New Name"} works without fetching the full JSON first. For simple changes (name, description, dates, usageLimit) a partial body is sufficient.

To ADD or MODIFY rules/actions: call get_promotion first, copy the existing rules/actions array, append or modify entries, then call update_promotion with the new array.

rules examples: [{"type":"item_total","configuration":{"CHANNEL_CODE":{"amount":5000}}}] — min order 50.00; [{"type":"cart_quantity","configuration":{"count":3}}] — min 3 items
actions examples: [{"type":"order_percentage_discount","configuration":{"percentage":0.1}}] — 10%% off order; [{"type":"order_fixed_discount","configuration":{"CHANNEL_CODE":{"amount":1000}}}] — fixed 10.00 off (ALL channels required)
Note: fixed-discount rules/actions missing channels are auto-filled with zero amounts.
Note: if description is no longer needed pass null (not empty string "") — Sylius requires description to be null or at least 2 characters.
DESC,
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(string $code, string $body): string
    {
        /** @var array<string, mixed> $b */
        $b = json_decode($body, true) ?? [];
        // Sylius requires description to be null or ≥2 chars; convert "" to null
        if (isset($b['description']) && $b['description'] === '') {
            $b['description'] = null;
        }
        if (isset($b['rules']) && \is_array($b['rules'])) {
            $b['rules'] = $this->stripAndFillChannels($b['rules']);
        }
        if (isset($b['actions']) && \is_array($b['actions'])) {
            $b['actions'] = $this->stripAndFillChannels($b['actions']);
        }

        return $this->client->put(sprintf('promotions/%s', $code), $b);
    }

    /**
     * Strips JSON-LD meta (@id, @var, id) from each item, and for items with
     * per-channel configuration (keys that look like channel codes) auto-fills
     * any channels currently in the system that are missing from the stored config.
     *
     * @param array<int, array<string, mixed>> $items
     *
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

            /** @var array<string, mixed> $config */
            $config = \is_array($item['configuration'] ?? null) ? $item['configuration'] : [];
            if (!$this->hasChannelKeys($config)) {
                return $item;
            }

            // Lazy-fetch all channel codes once
            if ($allChannelCodes === null) {
                /** @var array<string, mixed> $channelsData */
                $channelsData = json_decode($this->client->get('channels', ['pagination' => false]), true);
                $allChannelCodes = array_column((array) ($channelsData['hydra:member'] ?? []), 'code');
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
     *
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

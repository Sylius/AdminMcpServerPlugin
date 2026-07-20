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

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_shipping_method',
    description: <<<'DESC'
update_shipping_method(code, body) → JSON of the updated shipping method.

IMPORTANT: First call get_shipping_method to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body. This preserves all required fields including translation @ids.

Convenience shorthands (only when you want to change the rate): pass amount (int, smallest currency unit e.g. 500=5.00) or percentage (float, e.g. 0.1=10%) instead of configuration — the tool builds configuration for all channels automatically.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code, string $body): string
    {
        $b = json_decode($body, true) ?? [];
        if (isset($b['amount']) || isset($b['percentage'])) {
            $channelsData = json_decode($this->client->get('channels', ['pagination' => false]), true);
            $allChannelCodes = array_column($channelsData['hydra:member'] ?? [], 'code');
            $calculator = $b['calculator'] ?? 'flat_rate';
            $usePercentage = str_contains($calculator, 'percentage');
            $b['configuration'] = array_fill_keys(
                $allChannelCodes,
                $usePercentage
                    ? ['amount' => (int) round(($b['percentage'] ?? 0) * 10000)]
                    : ['amount' => (int) ($b['amount'] ?? 0)],
            );
            unset($b['amount'], $b['percentage']);
        }

        return $this->client->put(sprintf('shipping-methods/%s', $code), $b);
    }
}

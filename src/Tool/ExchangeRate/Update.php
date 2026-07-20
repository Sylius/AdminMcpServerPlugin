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

namespace Sylius\AdminMcpServerPlugin\Tool\ExchangeRate;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_exchange_rate',
    description: 'update_exchange_rate(id, body) → JSON of the updated exchange rate. id is numeric (from list_exchange_rates). body (JSON string) — fields: ratio (float). Example: \'{"ratio": 0.95}\'',
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(int $id, string $body): string
    {
        return $this->client->put(sprintf('exchange-rates/%d', $id), json_decode($body, true) ?? []);
    }
}

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
    name: 'get_exchange_rate',
    description: 'get_exchange_rate(id) → JSON object of a single exchange rate. Returns: id, ratio, sourceCurrency (IRI), targetCurrency (IRI). Use list_exchange_rates to find the numeric id.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $id Numeric exchange rate ID (from list_exchange_rates).
     */
    public function __invoke(int $id): string
    {
        return $this->client->get(sprintf('exchange-rates/%d', $id));
    }
}

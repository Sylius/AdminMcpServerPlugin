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

namespace Sylius\AdminMcpServerPlugin\Tool\Currency;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'create_currency',
    description: 'create_currency(code) → JSON object of the newly created Sylius currency. code must be a valid ISO 4217 currency code (e.g. "USD", "EUR", "PLN", "GBP").',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code ISO 4217 currency code (e.g. "USD", "EUR", "PLN").
     */
    public function __invoke(string $code): string
    {
        return $this->client->post('currencies', ['code' => $code]);
    }
}

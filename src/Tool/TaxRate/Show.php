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

namespace Sylius\AdminMcpServerPlugin\Tool\TaxRate;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_tax_rate',
    description: 'get_tax_rate(code) → JSON object of a single Sylius tax rate. Returns: id, code, name, amount, includedInPrice, calculator, category, zone, startDate, endDate.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Tax rate code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('tax-rates/%s', $code));
    }
}

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
    name: 'list_tax_rates',
    description: 'list_tax_rates(page?, itemsPerPage?) → JSON Hydra collection of Sylius tax rates. Each rate has: id, code, name, amount (float, e.g. 0.07 = 7%%), includedInPrice, calculator, category (IRI), zone (IRI), startDate, endDate.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $page         Page number (1-based). Default = 1.
     * @param int $itemsPerPage Items per page. Default = 30.
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30): string
    {
        return $this->client->get('tax-rates', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

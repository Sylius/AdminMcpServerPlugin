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
    name: 'create_tax_rate',
    description: 'create_tax_rate(code, name, amount, category, zone, includedInPrice?, calculator?) → JSON object of the newly created Sylius tax rate. amount is a float (e.g. 0.23 = 23%). category is the IRI from list_tax_categories @id. zone is the IRI from list_zones @id. calculator defaults to "default".',
)]
final readonly class Create
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code             Unique tax rate code (e.g. "vat_23").
     * @param string $name             Display name (e.g. "VAT 23%").
     * @param float  $amount           Tax rate as a decimal (e.g. 0.23 for 23%).
     * @param string $category         Tax category IRI from list_tax_categories @id.
     * @param string $zone             Zone IRI from list_zones @id.
     * @param bool   $includedInPrice  Whether the tax is included in the displayed price. Default = false.
     * @param string $calculator       Calculator type. Default = "default".
     */
    public function __invoke(
        string $code,
        string $name,
        float $amount,
        string $category,
        string $zone,
        bool $includedInPrice = false,
        string $calculator = 'default',
    ): string {
        return $this->client->post('tax-rates', [
            'code' => $code,
            'name' => $name,
            'amount' => $amount,
            'includedInPrice' => $includedInPrice,
            'calculator' => $calculator,
            'category' => $category,
            'zone' => $zone,
        ]);
    }
}

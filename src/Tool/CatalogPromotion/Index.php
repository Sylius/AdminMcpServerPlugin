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

namespace Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_catalog_promotions',
    description: 'list_catalog_promotions(page?, itemsPerPage?) → Lists catalog promotions (discounts shown directly on product prices in the catalog, before checkout). Each has: code, name, enabled, state (active/inactive/processing — processing means Sylius is still applying it), scopes (which products), actions (what discount), startDate/endDate. Use get_catalog_promotion(code) for full details.',
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
        return $this->client->get('catalog-promotions', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

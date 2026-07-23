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
    description: 'list_catalog_promotions(page?, itemsPerPage?, orderBy?, orderDir?) → Lists catalog promotions (discounts shown directly on product prices in the catalog, before checkout). Each has: code, name, enabled, state (active/inactive/processing — processing means Sylius is still applying it), scopes (which products), actions (what discount), startDate/endDate. Use get_catalog_promotion(code) for full details. Use orderBy/orderDir to sort (e.g. orderBy=createdAt orderDir=desc).',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(
        int $page = 1,
        int $itemsPerPage = 30,
        string $orderBy = '',
        string $orderDir = 'asc',
    ): string {
        $params = [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ];
        if ($orderBy !== '') {
            $params['order[' . $orderBy . ']'] = $orderDir;
        }

        return $this->client->get('catalog-promotions', $params);
    }
}

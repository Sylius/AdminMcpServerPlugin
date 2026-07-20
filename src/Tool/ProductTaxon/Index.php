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

namespace Sylius\AdminMcpServerPlugin\Tool\ProductTaxon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_product_taxons',
    description: 'list_product_taxons(page?, itemsPerPage?, productCode?, taxonCode?) → JSON Hydra collection of Sylius product-taxon assignments. Each entry has: id, product (IRI), taxon (IRI), position.',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int    $page         Page number (1-based). Default = 1.
     * @param int    $itemsPerPage Items per page. Default = 30.
     * @param string $productCode  Filter by product code. Default = "" (all).
     * @param string $taxonCode    Filter by taxon code. Default = "" (all).
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30, string $productCode = '', string $taxonCode = ''): string
    {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        if ($productCode !== '') {
            $params['product.code'] = $productCode;
        }
        if ($taxonCode !== '') {
            $params['taxon.code'] = $taxonCode;
        }

        return $this->client->get('product-taxons', $params);
    }
}

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

namespace Sylius\AdminMcpServerPlugin\Tool\Taxon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_taxons',
    description: <<<'DESC'
list_taxons(page?, itemsPerPage?, parentCode?) → JSON-LD/Hydra list of product categories (taxons). The taxonomy is a tree: root categories have no parent, subcategories have a parentCode.

Each taxon has: code (string — the identifier for get_taxon, update_taxon, delete_taxon), enabled, parent (JSON-LD IRI — last segment is the parent code), children (JSON-LD IRIs — last segment of each is the child code), position, translations (name, slug per locale).

Filter by parentCode to see only direct children of a category (e.g. parentCode="caps" shows "simple_caps" and "caps_with_pompons"). To see all categories at once use a large itemsPerPage. Use get_taxon(code) for full details of a specific category.
DESC,
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(int $page = 1, int $itemsPerPage = 30, string $parentCode = ''): string
    {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        if ($parentCode !== '') {
            $params['parent'] = sprintf('/api/v2/admin/taxons/%s', $parentCode);
        }

        return $this->client->get('taxons', $params);
    }
}

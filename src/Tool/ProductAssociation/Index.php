<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociation;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_product_associations',
    description: 'list_product_associations(page?, itemsPerPage?, ownerCode?, typeCode?) → JSON-LD/Hydra collection of Sylius product associations. Each association has: @id (IRI — last path segment is the numeric id, e.g. /api/v2/admin/product-associations/42 → 42), type (association type IRI), owner (product IRI), associatedProducts (list of product IRIs). Use the numeric id in get_product_association, update_product_association, delete_product_association.',
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
     * @param string $ownerCode    Filter by owner product code. Default = "" (all).
     * @param string $typeCode     Filter by association type code. Default = "" (all).
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30, string $ownerCode = '', string $typeCode = ''): string
    {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        if ($ownerCode !== '') {
            $params['owner.code'] = $ownerCode;
        }
        if ($typeCode !== '') {
            $params['type.code'] = $typeCode;
        }

        return $this->client->get('product-associations', $params);
    }
}

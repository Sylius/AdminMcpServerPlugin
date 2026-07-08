<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductAssociation;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_product_associations',
    description: 'list_product_associations(page?, itemsPerPage?, ownerCode?, typeCode?) → JSON Hydra collection of Sylius product associations. Each association has: id, type (IRI), owner (product IRI), associatedProducts (list of product IRIs).',
)]
final readonly class Index
{
    public function __construct(
        private AdminApiClient $client,
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

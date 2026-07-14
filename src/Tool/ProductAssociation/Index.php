<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ProductAssociation;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_product_associations',
    description: 'list_product_associations(page?, itemsPerPage?, ownerCode?, typeCode?) → JSON-LD/Hydra collection of Sylius product associations. Each association has: id (numeric, extracted from @id — use in update_product_association, delete_product_association), type (JSON-LD IRI — last segment is the type code), owner (JSON-LD IRI — last segment is the owner product code), associatedProducts (list of JSON-LD IRIs — last segment of each is the product code).',
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

        $data = json_decode($this->client->get('product-associations', $params), true);

        foreach (array_keys($data['hydra:member'] ?? []) as $i) {
            if (isset($data['hydra:member'][$i]['@id']) && preg_match('/\/(\d+)$/', $data['hydra:member'][$i]['@id'], $m)) {
                $data['hydra:member'][$i]['id'] = (int) $m[1];
            }
        }

        return (string) json_encode($data);
    }
}

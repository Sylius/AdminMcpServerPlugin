<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\ProductAssociationType;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_product_association_types',
    description: 'list_product_association_types(page?, itemsPerPage?) → JSON Hydra collection of Sylius product association types. Each type has: id, code, name, translations (name per locale), createdAt, updatedAt.',
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
        return $this->client->get('product-association-types', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

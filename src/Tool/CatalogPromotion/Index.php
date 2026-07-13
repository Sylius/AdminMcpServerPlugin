<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\CatalogPromotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_catalog_promotions',
    description: 'list_catalog_promotions(page?, itemsPerPage?) → JSON Hydra collection of Sylius catalog promotions. Each item has: id, code, name, enabled, exclusive, priority, startDate, endDate, channels, scopes (type + configuration), actions (type + configuration), translations (label, description per locale), state.',
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

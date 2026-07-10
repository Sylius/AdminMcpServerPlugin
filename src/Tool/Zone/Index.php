<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Zone;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_zones',
    description: 'list_zones(page?, itemsPerPage?) → JSON Hydra collection of Sylius zones. Each zone has: id, code, name, type ("country"|"zone"|"province"), scope ("shipping"|"tax"|"all"), members (array of zone member IRIs). Use zone codes when creating shipping methods and tax rates.',
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
        return $this->client->get('zones', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

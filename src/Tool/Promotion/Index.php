<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Promotion;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_promotions',
    description: 'list_promotions(page?, itemsPerPage?) → JSON Hydra collection of Sylius cart promotions. Each item has: id, code, name, description, priority, exclusive, usageLimit, used, couponBased, channels, startsAt, endsAt, rules, actions.',
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
        return $this->client->get('promotions', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

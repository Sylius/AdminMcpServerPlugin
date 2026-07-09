<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ExchangeRate;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_exchange_rates',
    description: 'list_exchange_rates(page?, itemsPerPage?) → JSON Hydra collection of Sylius exchange rates. Each rate has: id, ratio, sourceCurrency, targetCurrency.',
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
        return $this->client->get('exchange-rates', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

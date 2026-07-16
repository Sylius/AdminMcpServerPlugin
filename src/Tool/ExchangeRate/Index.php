<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ExchangeRate;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_exchange_rates',
    description: 'list_exchange_rates(page?, itemsPerPage?, currencyCode?) → JSON Hydra collection of Sylius exchange rates. Each rate has: id, ratio, sourceCurrency (IRI), targetCurrency (IRI). currencyCode filters rates involving that currency (source or target). Use the id field with update_exchange_rate and delete_exchange_rate.',
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
     * @param string $currencyCode Filter by currency code (e.g. "USD"). Default = "" (all).
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30, string $currencyCode = ''): string
    {
        $params = ['page' => $page, 'itemsPerPage' => $itemsPerPage];
        if ($currencyCode !== '') {
            $params['currencyCode'] = $currencyCode;
        }

        return $this->client->get('exchange-rates', $params);
    }
}

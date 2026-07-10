<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Currency;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_currencies',
    description: 'list_currencies(page?, itemsPerPage?) → JSON Hydra collection of Sylius currencies. Each currency has: id, code (ISO 4217, e.g. "USD", "EUR", "PLN").',
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
        return $this->client->get('currencies', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

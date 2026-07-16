<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Locale;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_locales',
    description: 'list_locales(page?, itemsPerPage?) → JSON Hydra collection of Sylius locales. Each locale has: id, code (e.g. "en_US", "pl_PL", "de_DE"), name (human-readable, e.g. "English (United States)").',
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
        return $this->client->get('locales', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

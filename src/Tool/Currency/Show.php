<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Currency;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_currency',
    description: 'get_currency(code) → JSON object of a single Sylius currency. Returns: id, code (ISO 4217). Use list_currencies to see all available currency codes.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('currencies/%s', $code));
    }
}

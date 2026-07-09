<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Country;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_country',
    description: 'get_country(code) → JSON object of a single Sylius country. Returns: id, code (ISO 3166-1 alpha-2), enabled, provinces.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code ISO 3166-1 alpha-2 country code (e.g. "US", "PL", "DE").
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('countries/%s', $code));
    }
}

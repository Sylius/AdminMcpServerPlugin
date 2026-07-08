<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Country;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_country',
    description: 'create_country(code, enabled?) → JSON object of the newly created Sylius country. code must be ISO 3166-1 alpha-2 (e.g. "US", "PL", "DE", "FR").',
)]
final readonly class Create
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code    ISO 3166-1 alpha-2 country code (e.g. "PL", "DE").
     * @param bool   $enabled Whether the country is enabled. Default = true.
     */
    public function __invoke(string $code, bool $enabled = true): string
    {
        return $this->client->post('countries', [
            'code' => $code,
            'enabled' => $enabled,
        ]);
    }
}

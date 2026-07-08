<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Locale;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'create_locale',
    description: 'create_locale(code) → JSON object of the newly created Sylius locale. code must be a valid locale string (e.g. "en_US", "pl_PL", "de_DE", "fr_FR").',
)]
final readonly class Create
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code Locale code (e.g. "pl_PL", "de_DE").
     */
    public function __invoke(string $code): string
    {
        return $this->client->post('locales', ['code' => $code]);
    }
}

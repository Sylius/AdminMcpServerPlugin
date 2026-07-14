<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Locale;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_locale',
    description: 'get_locale(code) → JSON object of a single Sylius locale. Returns: id, code (e.g. "en_US"), name (human-readable, e.g. "English (United States)").',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('locales/%s', $code));
    }
}

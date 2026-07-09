<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Channel;

use Acme\SyliusExamplePlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_channel',
    description: 'get_channel(code) → JSON object of a single Sylius channel. Returns: id, code, name, hostname, color, enabled, defaultLocale, baseCurrency, locales, currencies, countries, taxZone, themeName, contactEmail.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Channel code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('channels/%s', $code));
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Country;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_country',
    description: 'update_country(code, enabled) → JSON object of the updated Sylius country. Uses PUT.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code    ISO 3166-1 alpha-2 country code to update.
     * @param bool   $enabled Whether the country is enabled.
     */
    public function __invoke(string $code, bool $enabled): string
    {
        return $this->client->put(sprintf('countries/%s', $code), [
            'enabled' => $enabled,
        ]);
    }
}

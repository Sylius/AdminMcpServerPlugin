<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Country;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_country',
    description: 'update_country(code, body) → JSON of the updated country. body (JSON string) — fields: enabled (bool). Example: \'{"enabled": true}\'',
)]
final readonly class Update
{
    public function __construct(private ApiClientInterface $client) {}

    public function __invoke(string $code, string $body): string
    {
        return $this->client->put(sprintf('countries/%s', $code), json_decode($body, true) ?? []);
    }
}

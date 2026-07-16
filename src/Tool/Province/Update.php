<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Province;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_province',
    description: <<<'DESC'
update_province(countryCode, provinceCode, body) → JSON of the updated province.

IMPORTANT: First call get_province to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body.

countryCode: 2-letter ISO code (e.g. "US"). provinceCode: full code including country prefix (e.g. "US-CA").
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $countryCode, string $provinceCode, string $body): string
    {
        return $this->client->put(
            sprintf('countries/%s/provinces/%s', $countryCode, $provinceCode),
            json_decode($body, true) ?? [],
        );
    }
}

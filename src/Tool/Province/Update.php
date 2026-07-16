<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Province;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_province',
    description: <<<'DESC'
update_province(countryCode, provinceCode, body) → JSON of the updated province. Only fields in body are changed.

countryCode: 2-letter ISO code (e.g. "US"). provinceCode: full code including country prefix (e.g. "US-CA").
body (JSON string) — fields: name (string), abbreviation (string).
Example: '{"name":"California","abbreviation":"CA"}'
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
        $existing = json_decode(
            $this->client->get(sprintf('countries/%s/provinces/%s', $countryCode, $provinceCode)),
            true,
        );
        $b = json_decode($body, true) ?? [];

        $merged = [
            'code' => $provinceCode,
            'name' => $b['name'] ?? ($existing['name'] ?? $provinceCode),
        ];

        $abbreviation = $b['abbreviation'] ?? ($existing['abbreviation'] ?? '');
        if ($abbreviation !== '') {
            $merged['abbreviation'] = $abbreviation;
        }

        return $this->client->put(
            sprintf('countries/%s/provinces/%s', $countryCode, $provinceCode),
            $merged,
        );
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Province;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_province',
    description: 'update_province(countryCode, provinceCode, name?, abbreviation?) → Updates a province. Only provided fields are changed. provinceCode is the full code including country prefix (e.g. "US-CA"). Returns JSON of the updated province.',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(
        string $countryCode,
        string $provinceCode,
        string $name = '',
        string $abbreviation = '',
    ): string {
        $existing = json_decode(
            $this->client->get(sprintf('countries/%s/provinces/%s', $countryCode, $provinceCode)),
            true,
        );

        $body = [
            'code' => $provinceCode,
            'name' => $name !== '' ? $name : ($existing['name'] ?? $provinceCode),
        ];
        $resolvedAbbreviation = $abbreviation !== '' ? $abbreviation : ($existing['abbreviation'] ?? '');
        if ($resolvedAbbreviation !== '') {
            $body['abbreviation'] = $resolvedAbbreviation;
        }

        return $this->client->put(
            sprintf('countries/%s/provinces/%s', $countryCode, $provinceCode),
            $body,
        );
    }
}

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Province;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'update_province',
    description: 'update_province(countryCode, provinceCode, name, abbreviation?) → JSON of the updated province. Uses PUT. provinceCode is the full code including country prefix (e.g. "US-CA").',
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $countryCode  2-letter ISO country code (e.g. "US").
     * @param string $provinceCode Full province code (e.g. "US-CA").
     * @param string $name         New province name.
     * @param string $abbreviation Optional abbreviation. Default = "".
     */
    public function __invoke(
        string $countryCode,
        string $provinceCode,
        string $name,
        string $abbreviation = '',
    ): string {
        $body = ['code' => $provinceCode, 'name' => $name];
        if ($abbreviation !== '') {
            $body['abbreviation'] = $abbreviation;
        }

        return $this->client->put(
            sprintf('countries/%s/provinces/%s', $countryCode, $provinceCode),
            $body,
        );
    }
}

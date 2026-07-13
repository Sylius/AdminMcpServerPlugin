<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Province;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_provinces',
    description: 'list_provinces(countryCode) → JSON Hydra collection of provinces for a given country. Each province has: id, code (e.g. "US-CA"), name, abbreviation. countryCode is the 2-letter ISO country code (e.g. "US", "CA", "DE").',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $countryCode 2-letter ISO country code (e.g. "US", "CA").
     * @param int    $page        Page number. Default = 1.
     * @param int    $itemsPerPage Items per page. Default = 30.
     */
    public function __invoke(string $countryCode, int $page = 1, int $itemsPerPage = 30): string
    {
        return $this->client->get(sprintf('countries/%s/provinces', $countryCode), [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

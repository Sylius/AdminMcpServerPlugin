<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Channel;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_channels',
    description: 'list_channels(page?, itemsPerPage?) → JSON Hydra collection of Sylius channels. Each channel has: id, code, name, hostname, color, enabled, defaultLocale, baseCurrency, locales, currencies, countries, taxZone, themeName, contactEmail, shippingAddressInCheckoutRequired.',
)]
final readonly class Index
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param int $page         Page number (1-based). Default = 1.
     * @param int $itemsPerPage Items per page. Default = 30.
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30): string
    {
        return $this->client->get('channels', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

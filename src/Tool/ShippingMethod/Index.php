<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_shipping_methods',
    description: 'list_shipping_methods(page?, itemsPerPage?) → JSON Hydra collection of Sylius shipping methods. Each method has: id, code, enabled, position, calculator, configuration (channel-keyed amounts), zone, category, taxCategory, channels, translations (name, description per locale).',
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param int $page         Page number (1-based). Default = 1.
     * @param int $itemsPerPage Items per page. Default = 30.
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30): string
    {
        return $this->client->get('shipping-methods', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

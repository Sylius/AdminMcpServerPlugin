<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'list_shipping_methods',
    description: 'list_shipping_methods(page?, itemsPerPage?) → Lists all shipping methods (delivery options shown at checkout). Each has: code, enabled, shippingChargesCalculator (flat_rate/per_unit_rate/percentage_discount), shippingChargesCalculatorConfiguration (cost per channel), zone, channels, translations (name per locale). Use get_shipping_method(code) for full details.',
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

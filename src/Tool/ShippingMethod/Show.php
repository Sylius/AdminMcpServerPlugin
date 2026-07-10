<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_shipping_method',
    description: 'get_shipping_method(code) → JSON object of a single Sylius shipping method. Returns: id, code, enabled, position, calculator, configuration, zone, category, taxCategory, channels, translations.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Shipping method code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('shipping-methods/%s', $code));
    }
}

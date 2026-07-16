<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_shipping_method',
    description: 'get_shipping_method(code) → Full details of a shipping method. Returns: code, enabled, shippingChargesCalculator (pricing type), shippingChargesCalculatorConfiguration (amount per channel — smallest currency unit, e.g. 500=5.00), zone (IRI — last segment is zone code), channels (list of IRIs), translations (name and description per locale), archivedAt (null if active).',
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

<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\PaymentMethod;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_payment_method',
    description: 'get_payment_method(code) → JSON object of a single Sylius payment method. Returns: id, code, enabled, position, gatewayConfig, channels, translations.',
)]
final readonly class Show
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code Payment method code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('payment-methods/%s', $code));
    }
}

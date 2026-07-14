<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\PaymentMethod;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_payment_method',
    description: 'delete_payment_method(code) → empty string on success (HTTP 204). Permanently deletes the Sylius payment method with the given code.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Payment method code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('payment-methods/%s', $code));
    }
}

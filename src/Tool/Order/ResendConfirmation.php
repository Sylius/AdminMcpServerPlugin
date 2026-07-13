<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'resend_order_confirmation',
    description: 'resend_order_confirmation(tokenValue) → Resends the order confirmation email to the customer. Returns empty string on success (HTTP 202).',
)]
final readonly class ResendConfirmation
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $tokenValue Order token value.
     */
    public function __invoke(string $tokenValue): string
    {
        return $this->client->patch(
            sprintf('orders/%s/resend-confirmation-email', $tokenValue),
            [],
        );
    }
}

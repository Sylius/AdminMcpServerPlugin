<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Order;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'resend_order_email',
    description: 'resend_order_email(tokenValue) → Resends the order confirmation email to the customer (useful if they didn\'t receive the original). The order must not be cancelled. tokenValue is the order token from list_orders or get_order. Returns empty string on success.',
)]
final readonly class ResendConfirmation
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    public function __invoke(string $tokenValue): string
    {
        return $this->client->post(sprintf('orders/%s/resend-confirmation-email', $tokenValue));
    }
}

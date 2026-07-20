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
    name: 'cancel_order',
    description: 'cancel_order(tokenValue) → Cancels a Sylius order. The order must be in a cancellable state (new, addressed, payment_selected, shipping_selected). Returns JSON of the cancelled order.',
)]
final readonly class Cancel
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $tokenValue Order token value to cancel.
     */
    public function __invoke(string $tokenValue): string
    {
        return $this->client->patch(sprintf('orders/%s/cancel', $tokenValue), []);
    }
}

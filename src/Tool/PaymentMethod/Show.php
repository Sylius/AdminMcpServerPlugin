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

namespace Sylius\AdminMcpServerPlugin\Tool\PaymentMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_payment_method',
    description: 'get_payment_method(code) → JSON object of a single Sylius payment method. Returns: id, code, enabled, position, gatewayConfig, channels, translations.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
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

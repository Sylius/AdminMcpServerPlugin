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

namespace Sylius\AdminMcpServerPlugin\Tool\ShippingMethod;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'restore_shipping_method',
    description: 'restore_shipping_method(code) → Restores a previously archived Sylius shipping method, making it available in the shop again. Returns JSON of the restored method.',
)]
final readonly class Restore
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Shipping method code to restore.
     */
    public function __invoke(string $code): string
    {
        return $this->client->patch(sprintf('shipping-methods/%s/restore', $code), []);
    }
}

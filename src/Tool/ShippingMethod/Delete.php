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
    name: 'delete_shipping_method',
    description: 'delete_shipping_method(code) → Deletes a Sylius shipping method permanently. Returns empty string on success (HTTP 204). Use archive_shipping_method instead if you want to keep it but hide it.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Shipping method code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('shipping-methods/%s', $code));
    }
}

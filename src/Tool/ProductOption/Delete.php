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

namespace Sylius\AdminMcpServerPlugin\Tool\ProductOption;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'delete_product_option',
    description: 'delete_product_option(code) → empty string on success (HTTP 204). Permanently deletes the Sylius product option with the given code. Cannot delete an option that is used by existing product variants.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Product option code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('product-options/%s', $code));
    }
}

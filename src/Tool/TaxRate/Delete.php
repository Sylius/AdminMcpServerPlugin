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

namespace Sylius\AdminMcpServerPlugin\Tool\TaxRate;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'delete_tax_rate',
    description: 'delete_tax_rate(code) → empty string on success (HTTP 204). Permanently deletes the Sylius tax rate with the given code.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Tax rate code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('tax-rates/%s', $code));
    }
}

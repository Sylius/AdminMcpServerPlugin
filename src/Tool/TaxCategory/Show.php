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

namespace Sylius\AdminMcpServerPlugin\Tool\TaxCategory;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'get_tax_category',
    description: 'get_tax_category(code) → JSON object of a single Sylius tax category. Returns: id, code, name, description, createdAt, updatedAt.',
)]
final readonly class Show
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Tax category code.
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('tax-categories/%s', $code));
    }
}

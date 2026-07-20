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

namespace Sylius\AdminMcpServerPlugin\Tool\Taxon;

use Mcp\Capability\Attribute\McpTool;
use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;

#[McpTool(
    name: 'delete_taxon',
    description: 'delete_taxon(code) → Permanently deletes a category and ALL its subcategories (cascade). Products assigned to the deleted category are NOT deleted — they just lose the category assignment. Returns empty string on success. Use list_taxons(parentCode=code) to preview what subcategories will also be removed before deleting.',
)]
final readonly class Delete
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    /**
     * @param string $code Taxon code to delete.
     */
    public function __invoke(string $code): string
    {
        return $this->client->delete(sprintf('taxons/%s', $code));
    }
}

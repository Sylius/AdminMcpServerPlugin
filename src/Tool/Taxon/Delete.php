<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Taxon;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

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

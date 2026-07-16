<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Taxon;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_taxons',
    description: <<<'DESC'
list_taxons(page?, itemsPerPage?, parentCode?) → JSON-LD/Hydra list of product categories (taxons). The taxonomy is a tree: root categories have no parent, subcategories have a parentCode.

Each taxon has: code (string — the identifier for get_taxon, update_taxon, delete_taxon), enabled, parent (JSON-LD IRI — last segment is the parent code), children (JSON-LD IRIs — last segment of each is the child code), position, translations (name, slug per locale).

Filter by parentCode to see only direct children of a category (e.g. parentCode="caps" shows "simple_caps" and "caps_with_pompons"). To see all categories at once use a large itemsPerPage. Use get_taxon(code) for full details of a specific category.
DESC,
)]
final readonly class Index
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(int $page = 1, int $itemsPerPage = 30, string $parentCode = ''): string
    {
        // The Sylius API does not support collection filtering for taxons.
        // When parentCode is provided, fetch the parent and return its direct children.
        if ($parentCode !== '') {
            $parent = json_decode($this->client->get(sprintf('taxons/%s', $parentCode)), true);
            $childIris = $parent['children'] ?? [];

            $children = [];
            foreach ($childIris as $iri) {
                $code = basename($iri);
                $children[] = json_decode($this->client->get(sprintf('taxons/%s', $code)), true);
            }

            return (string) json_encode([
                '@context' => '/api/v2/contexts/Taxon',
                '@type' => 'hydra:Collection',
                'hydra:totalItems' => count($children),
                'hydra:member' => $children,
            ]);
        }

        return $this->client->get('taxons', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

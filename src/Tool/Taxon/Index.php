<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Taxon;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'list_taxons',
    description: 'list_taxons(page?, itemsPerPage?) → JSON Hydra collection of Sylius taxons (categories). Each taxon has: id, code, enabled, parent (IRI), children, position, translations (name, slug per locale).',
)]
final readonly class Index
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param int $page         Page number (1-based). Default = 1.
     * @param int $itemsPerPage Items per page. Default = 30.
     */
    public function __invoke(int $page = 1, int $itemsPerPage = 30): string
    {
        return $this->client->get('taxons', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ]);
    }
}

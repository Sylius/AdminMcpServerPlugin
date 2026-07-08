<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Taxon;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'get_taxon',
    description: 'get_taxon(code) → JSON object of a single Sylius taxon. Returns: id, code, enabled, parent (IRI), children, position, translations (name, slug per locale).',
)]
final readonly class Show
{
    public function __construct(
        private AdminApiClient $client,
    ) {
    }

    /**
     * @param string $code Taxon code (e.g. "t_shirts", "category").
     */
    public function __invoke(string $code): string
    {
        return $this->client->get(sprintf('taxons/%s', $code));
    }
}

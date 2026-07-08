<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Tool\Taxon;

use Acme\SyliusExamplePlugin\Http\AdminApiClient;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'delete_taxon',
    description: 'delete_taxon(code) → empty string on success (HTTP 204). Permanently deletes the Sylius taxon with the given code. Also deletes all child taxons.',
)]
final readonly class Delete
{
    public function __construct(
        private AdminApiClient $client,
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

<?php

declare(strict_types=1);

namespace Sylius\AdminMcpServerPlugin\Tool\Taxon;

use Sylius\AdminMcpServerPlugin\Api\ApiClientInterface;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(
    name: 'update_taxon',
    description: <<<'DESC'
update_taxon(code, body) → JSON of the updated taxon (product category).

IMPORTANT: First call get_taxon to get the current JSON, then modify only the fields you want to change, and pass the full modified JSON as body. This preserves all required fields including translation @ids.

NOTE: slug does NOT auto-update when you change the name; update it separately if needed.
DESC,
)]
final readonly class Update
{
    public function __construct(
        private ApiClientInterface $client,
    ) {
    }

    public function __invoke(string $code, string $body): string
    {
        return $this->client->put(sprintf('taxons/%s', $code), json_decode($body, true) ?? []);
    }
}
